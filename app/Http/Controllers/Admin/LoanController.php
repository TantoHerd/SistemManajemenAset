<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Loan;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LoanController extends Controller
{
    /**
     * Display a listing of loans.
     */
    public function index(Request $request)
    {
        $query = Loan::with(['asset', 'user', 'approver'])->latest();

        // Filter
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('loan_code', 'like', "%{$request->search}%")
                  ->orWhereHas('asset', fn($q) => $q->where('name', 'like', "%{$request->search}%"))
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
            });
        }

        $loans = $query->paginate(15);
        $statuses = [
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'active' => 'Aktif',
            'returned' => 'Dikembalikan',
            'overdue' => 'Terlambat',
            'rejected' => 'Ditolak',
        ];

        return view('admin.loans.index', compact('loans', 'statuses'));
    }

    /**
     * Show form create.
     */
    public function create(Request $request)
    {
        $asset = null;
        if ($request->asset_id) {
            $asset = Asset::findOrFail($request->asset_id);
        }

        $assets = Asset::where('status', 'available')->orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('admin.loans.create', compact('assets', 'users', 'asset'));
    }

    /**
     * Store loan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'user_id' => 'required|exists:users,id',
            'loan_date' => 'required|date',
            'expected_return_date' => 'required|date|after_or_equal:loan_date',
            'purpose' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
            'condition_before' => 'nullable|string|max:500',
        ]);

        // Cek aset available
        $asset = Asset::find($request->asset_id);
        if ($asset->status !== 'available') {
            return back()->with('error', 'Aset tidak tersedia untuk dipinjam')->withInput();
        }

        $validated['loan_code'] = Loan::generateCode();
        $validated['status'] = auth()->user()->role === 'admin' ? 'approved' : 'pending';

        $loan = Loan::create($validated);

        // Auto-approve untuk admin
        if ($loan->status === 'approved') {
            $loan->update(['approved_by' => auth()->id()]);
            $this->approveLoan($loan);
        }

        return redirect()->route('admin.loans.index')
            ->with('success', 'Pengajuan peminjaman berhasil dibuat');
    }

    /**
     * Show detail.
     */
    public function show(Loan $loan)
    {
        $loan->load(['asset', 'user', 'approver']);
        return view('admin.loans.show', compact('loan'));
    }

    /**
     * Approve loan.
     */
    public function approve(Loan $loan)
    {
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Peminjaman tidak dalam status pending');
        }

        $loan->update([
            'status' => Loan::STATUS_APPROVED,
            'approved_by' => auth()->id()
        ]);

        $this->approveLoan($loan);

        return back()->with('success', 'Peminjaman disetujui');
    }

    /**
     * Reject loan.
     */
    public function reject(Request $request, Loan $loan)
    {
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Peminjaman tidak dalam status pending');
        }

        $loan->update([
            'status' => Loan::STATUS_REJECTED,
            'approved_by' => auth()->id(),
            'notes' => $loan->notes . "\nAlasan ditolak: " . $request->reason
        ]);

        return back()->with('success', 'Peminjaman ditolak');
    }

    /**
     * Return asset.
     */
    public function return(Request $request, Loan $loan)
    {
        if (!in_array($loan->status, [Loan::STATUS_ACTIVE, Loan::STATUS_OVERDUE])) {
            return back()->with('error', 'Status peminjaman tidak valid untuk dikembalikan');
        }

        $actualReturnDate = now();
        $fine = 0;

        // Hitung denda jika terlambat
        if ($actualReturnDate->gt($loan->expected_return_date)) {
            $daysLate = $actualReturnDate->diffInDays($loan->expected_return_date);
            $fine = $daysLate * 10000; // Rp 10.000/hari
        }

        $loan->update([
            'status' => Loan::STATUS_RETURNED,
            'actual_return_date' => $actualReturnDate,
            'condition_after' => $request->condition_after,
            'fine_amount' => $fine
        ]);

        // Update status aset jadi available
        $loan->asset->update([
            'status' => 'available',
            'assigned_to' => null
        ]);

        return back()->with('success', 'Aset berhasil dikembalikan' . ($fine > 0 ? '. Denda: Rp ' . number_format($fine, 0, ',', '.') : ''));
    }

    /**
     * Process approval (update status aset).
     */
    private function approveLoan(Loan $loan)
    {
        // Set aset jadi in_use
        $loan->asset->update([
            'status' => 'in_use',
            'assigned_to' => $loan->user_id
        ]);

        // Update loan jadi active
        $loan->update(['status' => Loan::STATUS_ACTIVE]);
    }

    /**
     * Cancel loan.
     */
    public function cancel(Loan $loan)
    {
        if (!in_array($loan->status, ['pending', 'approved'])) {
            return back()->with('error', 'Peminjaman tidak dapat dibatalkan');
        }

        $loan->update(['status' => Loan::STATUS_CANCELLED]);

        return back()->with('success', 'Peminjaman dibatalkan');
    }

    /**
     * Print bukti peminjaman.
     */
    public function printReceipt(Loan $loan)
    {
        $loan->load(['asset', 'user', 'approver']);
        
        $companyName = \App\Models\Setting::where('key', 'company_name')->value('value') ?? 'PT. NAMA PERUSAHAAN';
        $systemName = \App\Models\Setting::where('key', 'system_name')->value('value') ?? 'SIMASET';
        $companyLogo = \App\Models\Setting::where('key', 'company_logo')->value('value') ?? null;

        // Encode logo jadi base64 untuk DomPDF
        $logoBase64 = null;
        if ($companyLogo) {
            $logoPath = storage_path('app/public/' . $companyLogo);
            if (file_exists($logoPath)) {
                $logoBase64 = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($logoPath));
            }
        }

        $pdf = Pdf::loadView('admin.loans.receipt', compact(
            'loan', 'companyName', 'systemName', 'companyLogo', 'logoBase64'
        ));
        
        $pdf->setPaper('A5', 'landscape'); // ← LANDSCAPE
        
        return $pdf->stream('bukti-peminjaman-' . $loan->loan_code . '.pdf');
    }
}
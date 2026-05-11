<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mecard;
use App\Models\Setting;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class MecardController extends Controller
{
    public function index()
    {
        $mecards = Mecard::latest()->paginate(10);
        return view('admin.mecards.index', compact('mecards'));
    }

    public function create()
    {
        return view('admin.mecards.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'note' => 'nullable|string|max:500',
        ]);

        Mecard::create($validated);

        return redirect()->route('admin.mecards.index')->with('success', 'MeCard berhasil dibuat');
    }

    public function show(Mecard $mecard)
    {
        $qrCode = QrCode::format('svg')->size(200)->margin(1)->generate($mecard->toMeCard());
        
        // Data untuk business card
        $companyName = Setting::get('company_name', 'PT. NAMA PERUSAHAAN');
        $systemName = Setting::get('system_name', 'SIMASET');
        $companyLogo = Setting::get('company_logo', null);
        
        return view('admin.mecards.show', compact('mecard', 'qrCode', 'companyName', 'systemName', 'companyLogo'));
    }

    public function edit(Mecard $mecard)
    {
        return view('admin.mecards.edit', compact('mecard'));
    }

    public function update(Request $request, Mecard $mecard)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'note' => 'nullable|string|max:500',
        ]);

        $mecard->update($validated);

        return redirect()->route('admin.mecards.index')->with('success', 'MeCard berhasil diupdate');
    }

    public function destroy(Mecard $mecard)
    {
        $mecard->delete();
        return redirect()->route('admin.mecards.index')->with('success', 'MeCard berhasil dihapus');
    }

    /**
     * Download QR Code PNG.
     */
    public function downloadQr(Mecard $mecard)
    {
        $qrCode = QrCode::format('png')->size(500)->margin(2)->generate($mecard->toMeCard());
        
        return response($qrCode)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="qr-' . $mecard->name . '.png"');
    }

    /**
     * Download MeCard text file.
     */
    public function downloadMecard(Mecard $mecard)
    {
        $content = $mecard->toMeCard();
        
        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $mecard->name . '.mecard"');
    }

    /**
     * Download MeCard sebagai PDF kartu nama.
     */
    public function downloadPdf(Mecard $mecard)
    {
        $qrPng = QrCode::format('png')->size(150)->margin(1)->generate($mecard->toMeCard());
        $qrBase64 = 'data:image/png;base64,' . base64_encode($qrPng);

        $companyLogo = Setting::get('company_logo', null);
        $logoBase64 = null;
        if ($companyLogo && file_exists(storage_path('app/public/' . $companyLogo))) {
            $logoPath = storage_path('app/public/' . $companyLogo);
            $logoBase64 = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><style>';
        $html .= '@page{size:90mm 55mm;margin:0;}';
        $html .= '*{margin:0;padding:0;box-sizing:border-box;}';
        $html .= 'body{width:90mm;height:55mm;overflow:hidden;font-family:sans-serif;margin:0;padding:0;}';
        $html .= '.card{width:90mm;height:55mm;background:#8B0000;padding:6mm;}';
        
        // Table layout
        $html .= 'table{width:100%;height:100%;border-collapse:collapse;}';
        $html .= 'td{padding:0;vertical-align:top;}';
        
        // Logo
        $html .= '.logo{width:32px;height:32px;border-radius:6px;background:rgba(255,255,255,0.15);display:inline-flex;align-items:center;justify-content:center;overflow:hidden;}';
        $html .= '.logo img{width:100%;height:100%;object-fit:contain;}';
        
        // Text
        $html .= '.name{font-size:12pt;font-weight:bold;color:#fff;}';
        $html .= '.title{font-size:6pt;color:rgba(255,255,255,0.7);}';
        $html .= '.info{font-size:6pt;color:rgba(255,255,255,0.85);line-height:1.3;}';
        $html .= '.contact{font-size:6pt;color:rgba(255,255,255,0.85);}';
        
        // QR
        $html .= '.qr-box{background:#fff;border-radius:6px;padding:3px;display:inline-block;}';
        $html .= '.qr-box img{width:16mm;height:16mm;display:block;}';
        
        $html .= '</style></head><body><div class="card"><table>';
        
        // Row 1: Logo + Nama + QR
        $html .= '<tr>';
        $html .= '<td style="width:38px;">';
        $html .= '<div class="logo">';
        if ($logoBase64) {
            $html .= '<img src="' . $logoBase64 . '" alt="Logo">';
        } else {
            $html .= '<span style="color:#fff;font-size:14px;">🏢</span>';
        }
        $html .= '</div></td>';
        
        $html .= '<td style="padding-left:6px;">';
        $html .= '<div class="name">' . $mecard->name . '</div>';
        if ($mecard->title) {
            $html .= '<div class="title">' . $mecard->title . '</div>';
        }
        $html .= '</td>';
        
        $html .= '<td style="width:48px;text-align:right;vertical-align:middle;">';
        $html .= '<div class="qr-box"><img src="' . $qrBase64 . '" alt="QR"></div>';
        $html .= '</td>';
        $html .= '</tr>';
        
        // Row 2: Info + Contact (GABUNG)
        $html .= '<tr><td colspan="3" style="vertical-align:bottom;padding-top:4px;">';
        $html .= '<div class="info">';
        if ($mecard->company) $html .= '🏢 ' . $mecard->company . '<br>';
        if ($mecard->address) $html .= '📍 ' . $mecard->address . '<br>';
        $contacts = [];
        if ($mecard->phone) $contacts[] = '📞 ' . $mecard->phone;
        if ($mecard->email) $contacts[] = '✉️ ' . $mecard->email;
        $html .= implode(' | ', $contacts);
        $html .= '</div></td></tr>';
        
        $html .= '</table></div></body></html>';

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper([0, 0, 255, 156], 'portrait');

        return $pdf->stream('kartu-nama-' . str_replace(' ', '-', $mecard->name) . '.pdf');
    }

    /**
     * Print kartu nama digital.
     */
    public function printCard(Mecard $mecard)
    {
        $companyName = Setting::get('company_name', 'PT. NAMA PERUSAHAAN');
        $systemName = Setting::get('system_name', 'SIMASET');
        $companyLogo = Setting::get('company_logo', null);
        $companyPhone = Setting::get('company_phone', '');
        $companyEmail = Setting::get('company_email', '');
        $companyAddress = Setting::get('company_address', '');

        return view('admin.mecards.print', compact(
            'mecard', 'companyName', 'systemName', 'companyLogo', 'companyPhone', 'companyEmail', 'companyAddress'
        ));
    }

    /**
     * Preview kartu nama (tanpa auto-print).
     */
    public function previewCard(Mecard $mecard)
    {
        $companyName = Setting::get('company_name', 'PT. NAMA PERUSAHAAN');
        $systemName = Setting::get('system_name', 'SIMASET');
        $companyLogo = Setting::get('company_logo', null);

        return view('admin.mecards.preview', compact(
            'mecard', 'companyName', 'systemName', 'companyLogo'
        ));
    }
}
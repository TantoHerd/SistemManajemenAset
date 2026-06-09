<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MeCard;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use App\Models\Setting;

class MeCardController extends Controller
{
    public function index()
    {
        $mecards = MeCard::latest()->paginate(10);
        return view('admin.mecards.index', compact('mecards'));
    }

    public function create()
    {
        return view('admin.mecards.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'phones' => 'nullable|array',
            'emails' => 'nullable|array',
            'addresses' => 'nullable|array',
            'socials' => 'nullable|array',
            'custom_fields' => 'nullable|array',
            'note' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except('logo');
        
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('mecards/logos', 'public');
            $data['logo_path'] = $logoPath;
        }
        
        // Process arrays
        $data['phones'] = array_values(array_filter($request->phones ?? [], function($phone) {
            return !empty($phone['number']);
        }));
        
        $data['emails'] = array_values(array_filter($request->emails ?? [], function($email) {
            return !empty($email['address']);
        }));
        
        $data['addresses'] = array_values(array_filter($request->addresses ?? [], function($address) {
            return !empty($address['text']);
        }));
        
        $data['socials'] = array_values(array_filter($request->socials ?? [], function($social) {
            return !empty($social['url']);
        }));
        
        $data['custom_fields'] = array_values(array_filter($request->custom_fields ?? [], function($field) {
            return !empty($field['label']) && !empty($field['value']);
        }));
        
        // Set main fields for compatibility
        $data['phone'] = $data['phones'][0]['number'] ?? null;
        $data['email'] = $data['emails'][0]['address'] ?? null;
        $data['address'] = $data['addresses'][0]['text'] ?? null;
        $data['website'] = $data['socials'][0]['url'] ?? null;
        
        MeCard::create($data);
        
        return redirect()->route('admin.mecards.index')
            ->with('success', 'MeCard berhasil dibuat!');
    }

    public function show(MeCard $mecard)
    {
        // Generate dengan ukuran SANGAT BESAR (1000px)
        $qrCode = QrCode::format('png')
            ->size(1000)                    // Generate di 1000x1000 pixel
            ->margin(4)                     // Margin besar untuk ruang putih
            ->errorCorrection('H')          // High error correction
            ->generate($mecard->toMeCard()); // Data lengkap
        
        $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCode);
        
        return view('admin.mecards.show', compact('mecard', 'qrCodeBase64'));
    }

    public function edit(MeCard $mecard)
    {
        return view('admin.mecards.edit', compact('mecard'));
    }

    public function update(Request $request, MeCard $mecard)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'phones' => 'nullable|array',
            'emails' => 'nullable|array',
            'addresses' => 'nullable|array',
            'socials' => 'nullable|array',
            'custom_fields' => 'nullable|array',
            'note' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except('logo');
        
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('mecards/logos', 'public');
            $data['logo_path'] = $logoPath;
        }
        
        // Process arrays
        $data['phones'] = array_values(array_filter($request->phones ?? [], function($phone) {
            return !empty($phone['number']);
        }));
        
        $data['emails'] = array_values(array_filter($request->emails ?? [], function($email) {
            return !empty($email['address']);
        }));
        
        $data['addresses'] = array_values(array_filter($request->addresses ?? [], function($address) {
            return !empty($address['text']);
        }));
        
        $data['socials'] = array_values(array_filter($request->socials ?? [], function($social) {
            return !empty($social['url']);
        }));
        
        $data['custom_fields'] = array_values(array_filter($request->custom_fields ?? [], function($field) {
            return !empty($field['label']) && !empty($field['value']);
        }));
        
        // Set main fields for compatibility
        $data['phone'] = $data['phones'][0]['number'] ?? null;
        $data['email'] = $data['emails'][0]['address'] ?? null;
        $data['address'] = $data['addresses'][0]['text'] ?? null;
        $data['website'] = $data['socials'][0]['url'] ?? null;
        
        $mecard->update($data);
        
        return redirect()->route('admin.mecards.index')
            ->with('success', 'MeCard berhasil diperbarui!');
    }

    public function destroy(MeCard $mecard)
    {
        $mecard->delete();
        
        return redirect()->route('admin.mecards.index')
            ->with('success', 'MeCard berhasil dihapus!');
    }

    public function downloadQR(MeCard $mecard)
    {
        $qrCode = QrCode::format('png')
            ->size(1200)                    // Sangat besar untuk download
            ->margin(4)
            ->errorCorrection('H')
            ->generate($mecard->toMeCard());
        
        $fileName = \Illuminate\Support\Str::slug($mecard->name) . '.png';
        
        return response($qrCode)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function print(MeCard $mecard)
{
    // Gunakan format SVG untuk print (tidak pecah)
    $qrCode = QrCode::format('svg')
        ->size(300)
        ->margin(2)
        ->errorCorrection('H')
        ->generate($mecard->toMeCard());
    
    return view('admin.mecards.print', compact('mecard', 'qrCode'));
}
}
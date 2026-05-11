<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'company_name' => Setting::get('company_name', 'PT. NAMA PERUSAHAAN'),
            'company_logo' => Setting::get('company_logo', null),
            'company_address' => Setting::get('company_address', ''),
            'company_phone' => Setting::get('company_phone', ''),
            'company_email' => Setting::get('company_email', ''),
            'system_name' => Setting::get('system_name', 'Sistem Manajemen Aset IT'),
            'system_favicon' => Setting::get('system_favicon', null),
            'maintenance_reminder_days' => Setting::get('maintenance_reminder_days', 7),
            'date_format' => Setting::get('date_format', 'd/m/Y'),
            'currency_symbol' => Setting::get('currency_symbol', 'Rp'),
            'timezone' => Setting::get('timezone', 'Asia/Jakarta'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function updateGeneral(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'company_address' => 'nullable|string',
            'company_phone' => 'nullable|string|max:20',
            'company_email' => 'nullable|email|max:255',
            'system_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Setting::set('company_name', $request->company_name);
        Setting::set('company_address', $request->company_address);
        Setting::set('company_phone', $request->company_phone);
        Setting::set('company_email', $request->company_email);
        Setting::set('system_name', $request->system_name);

        return redirect()->back()->with('success', 'Pengaturan umum berhasil disimpan');
    }

    public function updateLogo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'system_favicon' => 'nullable|image|mimes:ico,png|max:512',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle Logo Upload
        if ($request->hasFile('company_logo')) {
            // Delete old logo
            $oldLogo = Setting::get('company_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $file = $request->file('company_logo');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $path = $file->storeAs('logo', $filename, 'public');
            
            Setting::set('company_logo', 'logo/' . $filename);
        }

        // Handle Favicon Upload
        if ($request->hasFile('system_favicon')) {
            // Delete old favicon
            $oldFavicon = Setting::get('system_favicon');
            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }

            $file = $request->file('system_favicon');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $path = $file->storeAs('favicon', $filename, 'public');
            
            Setting::set('system_favicon', 'favicon/' . $filename);
        }

        return redirect()->back()->with('success', 'Logo dan favicon berhasil diupload');
    }

    public function updatePreferences(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'maintenance_reminder_days' => 'required|integer|min:1|max:30',
            'date_format' => 'required|string',
            'currency_symbol' => 'required|string|max:10',
            'timezone' => 'required|string', // ← TAMBAH
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Setting::set('maintenance_reminder_days', $request->maintenance_reminder_days);
        Setting::set('date_format', $request->date_format);
        Setting::set('currency_symbol', $request->currency_symbol);
        Setting::set('timezone', $request->timezone); // ← TAMBAH

        return redirect()->back()->with('success', 'Preferensi berhasil disimpan');
    }

    public function removeLogo()
    {
        $oldLogo = Setting::get('company_logo');
        if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
            Storage::disk('public')->delete($oldLogo);
        }
        Setting::set('company_logo', null);

        return redirect()->back()->with('success', 'Logo berhasil dihapus');
    }

    public function removeFavicon()
    {
        $oldFavicon = Setting::get('system_favicon');
        if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
            Storage::disk('public')->delete($oldFavicon);
        }
        Setting::set('system_favicon', null);

        return redirect()->back()->with('success', 'Favicon berhasil dihapus');
    }

    /**
     * Download VCard QR Code untuk kontak perusahaan.
     */
    public function vcardQr()
    {
        $companyName = Setting::get('company_name', 'PT. NAMA PERUSAHAAN');
        $companyPhone = Setting::get('company_phone', '');
        $companyEmail = Setting::get('company_email', '');
        $companyAddress = Setting::get('company_address', '');
        $systemName = Setting::get('system_name', 'SIMASET');

        // Format MECARD (NTT Docomo standard - paling universal)
        $mecard = "MECARD:";
        $mecard .= "N:{$companyName};";
        if ($companyPhone) $mecard .= "TEL:{$companyPhone};";
        if ($companyEmail) $mecard .= "EMAIL:{$companyEmail};";
        if ($companyAddress) $mecard .= "ADR:{$companyAddress};";
        $mecard .= "NOTE:{$systemName};";
        $mecard .= ";";

        $qrCode = QrCode::format('svg')
            ->size(300)
            ->margin(2)
            ->generate($mecard);

        return response($qrCode)->header('Content-Type', 'image/svg+xml');
    }

    /**
     * Tampilkan kartu nama digital perusahaan.
     */
    public function businessCard()
    {
        $companyName = Setting::get('company_name', 'PT. NAMA PERUSAHAAN');
        $companyPhone = Setting::get('company_phone', '');
        $companyEmail = Setting::get('company_email', '');
        $companyAddress = Setting::get('company_address', '');
        $systemName = Setting::get('system_name', 'SIMASET');
        $companyLogo = Setting::get('company_logo', null);

        return view('admin.settings.business-card', compact(
            'companyName', 'companyPhone', 'companyEmail', 
            'companyAddress', 'systemName', 'companyLogo'
        ));
    }

    /**
     * Download kartu nama sebagai JPG.
     */
    public function downloadBusinessCard()
    {
        $companyName = Setting::get('company_name', 'PT. NAMA PERUSAHAAN');
        $companyPhone = Setting::get('company_phone', '');
        $companyEmail = Setting::get('company_email', '');
        $companyAddress = Setting::get('company_address', '');
        $systemName = Setting::get('system_name', 'SIMASET');
        $companyLogo = Setting::get('company_logo', null);

        // Ukuran kartu 90mm x 55mm pada 300 DPI
        $width = 1063;  // 90mm @ 300dpi
        $height = 650;  // 55mm @ 300dpi
        
        // Buat canvas
        $image = imagecreatetruecolor($width, $height);
        
        // Background gelap
        $bgColor = imagecolorallocate($image, 30, 30, 47);
        imagefill($image, 0, 0, $bgColor);
        
        // Warna
        $white = imagecolorallocate($image, 255, 255, 255);
        $gray = imagecolorallocate($image, 170, 170, 170);
        $lightGray = imagecolorallocate($image, 204, 204, 204);
        $whiteTransparent = imagecolorallocatealpha($image, 255, 255, 255, 90);

        // Font path (pakai font bawaan)
        $fontBold = public_path('fonts/arialbd.ttf');
        $fontRegular = public_path('fonts/arial.ttf');
        
        // Fallback ke font sistem
        if (!file_exists($fontBold)) $fontBold = 'C:\Windows\Fonts\arialbd.ttf';
        if (!file_exists($fontRegular)) $fontRegular = 'C:\Windows\Fonts\arial.ttf';

        // === LEFT SIDE ===
        $x = 50;
        $y = 80;

        // Logo placeholder
        if ($companyLogo && file_exists(public_path('storage/' . $companyLogo))) {
            $logoPath = public_path('storage/' . $companyLogo);
            $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
            if ($ext == 'png') {
                $logo = imagecreatefrompng($logoPath);
            } elseif ($ext == 'jpg' || $ext == 'jpeg') {
                $logo = imagecreatefromjpeg($logoPath);
            }
            if (isset($logo)) {
                $logoResized = imagescale($logo, 90, 90);
                imagecopy($image, $logoResized, $x, $y - 40, 0, 0, 90, 90);
                imagedestroy($logo);
                imagedestroy($logoResized);
            }
        }

        // Nama Perusahaan
        $y = 120;
        imagettftext($image, 28, 0, $x, $y, $white, $fontBold, $companyName);
        
        // Sistem
        $y += 25;
        imagettftext($image, 12, 0, $x, $y, $gray, $fontRegular, strtoupper($systemName));
        
        // Info kontak
        $y += 35;
        if ($companyPhone) {
            imagettftext($image, 13, 0, $x, $y, $lightGray, $fontRegular, "📞 " . $companyPhone);
            $y += 22;
        }
        if ($companyEmail) {
            imagettftext($image, 13, 0, $x, $y, $lightGray, $fontRegular, "✉️ " . $companyEmail);
            $y += 22;
        }
        if ($companyAddress) {
            imagettftext($image, 13, 0, $x, $y, $lightGray, $fontRegular, "📍 " . $companyAddress);
        }

        // === RIGHT SIDE - QR Code ===
        $qrSize = 200;
        $qrX = $width - $qrSize - 50;
        $qrY = ($height - $qrSize) / 2;

        // Generate QR PNG
        $mecard = "MECARD:";
        $mecard .= "N:{$companyName};";
        if ($companyPhone) $mecard .= "TEL:{$companyPhone};";
        if ($companyEmail) $mecard .= "EMAIL:{$companyEmail};";
        if ($companyAddress) $mecard .= "ADR:{$companyAddress};";
        $mecard .= "NOTE:{$systemName} - Sistem Manajemen Aset IT;";
        $mecard .= ";";

        $qrImage = QrCode::format('png')
            ->size($qrSize)
            ->margin(2)
            ->style('square')
            ->eye('square')
            ->generate($mecard);

        $qrTemp = tempnam(sys_get_temp_dir(), 'qr') . '.png';
        file_put_contents($qrTemp, $qrImage);
        $qrGd = imagecreatefrompng($qrTemp);
        
        // White background for QR
        $qrBgX = $qrX - 10;
        $qrBgY = $qrY - 10;
        $qrBgW = $qrSize + 20;
        $qrBgH = $qrSize + 20;
        imagefilledrectangle($image, $qrBgX, $qrBgY, $qrBgX + $qrBgW, $qrBgY + $qrBgH, $white);
        
        imagecopy($image, $qrGd, $qrX, $qrY, 0, 0, $qrSize, $qrSize);
        imagedestroy($qrGd);
        unlink($qrTemp);

        // Scan Me text
        $scanY = $qrY + $qrSize + 20;
        imagettftext($image, 10, 0, $qrX + 50, $scanY, $gray, $fontRegular, "Scan Me");

        // Output
        ob_start();
        imagejpeg($image, null, 95);
        $jpgData = ob_get_clean();
        imagedestroy($image);

        $filename = 'kartu-nama-' . str_replace(' ', '-', $companyName) . '.jpg';

        return response($jpgData)
            ->header('Content-Type', 'image/jpeg')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
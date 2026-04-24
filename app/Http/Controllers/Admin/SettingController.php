<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

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
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Setting::set('maintenance_reminder_days', $request->maintenance_reminder_days);
        Setting::set('date_format', $request->date_format);
        Setting::set('currency_symbol', $request->currency_symbol);

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
}
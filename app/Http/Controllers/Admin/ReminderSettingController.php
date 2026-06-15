<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReminderSetting;
use Illuminate\Http\Request;

class ReminderSettingController extends Controller
{
    public function index()
    {
        $settings = ReminderSetting::first();
        return view('admin.reminder.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'is_active' => 'boolean',
            'reminder_days' => 'array',
            'reminder_days.*' => 'integer|min:1|max:30',
            'email_notification' => 'boolean',
            'system_notification' => 'boolean',
            'send_time' => 'required|date_format:H:i',
        ]);

        $settings = ReminderSetting::first();
        
        $settings->update([
            'is_active' => $request->has('is_active'),
            'reminder_days' => $request->reminder_days ?? [7, 3, 1],
            'email_notification' => $request->has('email_notification'),
            'system_notification' => $request->has('system_notification'),
            'send_time' => $request->send_time,
        ]);

        return back()->with('success', 'Pengaturan reminder berhasil disimpan.');
    }

    public function test(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Kirim test email
        try {
            Mail::raw('Test email from SIMASET reminder system', function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Test Reminder Maintenance');
            });
            return back()->with('success', 'Test email berhasil dikirim ke ' . $request->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal kirim test email: ' . $e->getMessage());
        }
    }
}
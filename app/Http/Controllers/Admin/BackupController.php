<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use App\Models\BackupSetting;

class BackupController extends Controller
{
    public function index()
    {
        $backups = $this->getBackups();
        $backupSettings = BackupSetting::first();
        
        // Hitung total size
        $totalSize = 0;
        foreach ($backups as $backup) {
            $totalSize += $backup['size'];
        }
        
        return view('admin.backup.index', compact('backups', 'backupSettings', 'totalSize'));
    }

    public function create(Request $request)
    {
        try {
            Artisan::call('db:backup');
            $output = Artisan::output();
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Backup berhasil dibuat!']);
            }
            
            return redirect()->route('admin.backup.index')
                ->with('success', 'Backup database berhasil dibuat!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            
            return redirect()->route('admin.backup.index')
                ->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }

    public function download($fileName)
    {
        $path = storage_path('app/backups/' . $fileName);
        
        if (File::exists($path)) {
            return response()->download($path, $fileName, [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ]);
        }
        
        return redirect()->route('admin.backup.index')
            ->with('error', 'File backup tidak ditemukan!');
    }

    public function destroy($fileName)
    {
        $path = storage_path('app/backups/' . $fileName);
        
        if (File::exists($path)) {
            File::delete($path);
            return redirect()->route('admin.backup.index')
                ->with('success', 'Backup berhasil dihapus!');
        }
        
        return redirect()->route('admin.backup.index')
            ->with('error', 'File backup tidak ditemukan!');
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|string',
        ]);

        try {
            $file = storage_path('app/backups/' . $request->backup_file);
            
            if (!File::exists($file)) {
                throw new \Exception('File backup tidak ditemukan');
            }
            
            // Panggil command restore
            Artisan::call('db:restore', [
                'file' => $request->backup_file
            ]);
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Database berhasil direstore!']);
            }
            
            return redirect()->route('admin.backup.index')
                ->with('success', 'Database berhasil direstore!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            
            return redirect()->route('admin.backup.index')
                ->with('error', 'Gagal restore: ' . $e->getMessage());
        }
    }

    public function scheduleSettings()
    {
        $settings = BackupSetting::first() ?? new BackupSetting();
        return view('admin.backup.schedule', compact('settings'));
    }

    public function updateSchedule(Request $request)
    {
        $request->validate([
            'frequency' => 'required|in:daily,weekly,monthly',
            'time' => 'required|date_format:H:i',
            'keep_backups' => 'required|integer|min:1|max:100',
        ]);

        $setting = BackupSetting::updateOrCreate(
            ['id' => 1],
            [
                'frequency' => $request->frequency,
                'time' => $request->time,
                'keep_backups' => $request->keep_backups,
                'is_active' => $request->has('is_active'),
            ]
        );

        return redirect()->route('admin.backup.schedule')
            ->with('success', 'Pengaturan backup otomatis berhasil disimpan!');
    }

    public function runScheduledBackup()
    {
        $setting = BackupSetting::where('is_active', true)->first();
        
        if (!$setting) {
            return;
        }
        
        $now = Carbon::now();
        $backupTime = Carbon::parse($setting->time);
        
        $shouldRun = false;
        
        switch ($setting->frequency) {
            case 'daily':
                $shouldRun = true;
                break;
            case 'weekly':
                $shouldRun = ($now->dayOfWeek == Carbon::SUNDAY);
                break;
            case 'monthly':
                $shouldRun = ($now->day == 1);
                break;
        }
        
        if ($shouldRun && $now->format('H:i') == $backupTime->format('H:i')) {
            Artisan::call('db:backup --compress');
            
            // Hapus backup berdasarkan setting keep_backups
            $this->limitBackups($setting->keep_backups);
        }
    }
    
    private function limitBackups($keep)
    {
        $backups = $this->getBackups();
        $toDelete = array_slice($backups, $keep);
        
        foreach ($toDelete as $backup) {
            if (file_exists($backup['path'])) {
                unlink($backup['path']);
            }
        }
    }

    private function getBackups()
    {
        $backupPath = storage_path('app/backups');
        
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
            return [];
        }
        
        $files = glob($backupPath . '/backup_*.sql*');
        $backups = [];
        
        foreach ($files as $file) {
            $backups[] = [
                'name' => basename($file),
                'size' => File::size($file),
                'last_modified' => File::lastModified($file),
                'path' => $file,
                'type' => str_ends_with($file, '.gz') ? 'Compressed' : 'SQL'
            ];
        }
        
        // Sort by last modified (newest first)
        usort($backups, function($a, $b) {
            return $b['last_modified'] - $a['last_modified'];
        });
        
        return $backups;
    }
}
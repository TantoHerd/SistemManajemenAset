<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cctv;
use Illuminate\Http\Request;
use App\Imports\CctvsImport;
use App\Exports\CctvTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class CctvController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 12);
        $cctvs = Cctv::latest()->paginate($perPage);
        return view('admin.cctvs.index', compact('cctvs', 'perPage'));
    }

    public function create()
    {
        return view('admin.cctvs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'username' => 'nullable|string|max:100',
            'password' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'stream_url' => 'nullable|string|max:500',
            'snapshot_url' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        Cctv::create($validated);

        return redirect()->route('admin.cctvs.index')->with('success', 'CCTV berhasil ditambahkan');
    }

    public function show(Cctv $cctv)
    {
        return view('admin.cctvs.show', compact('cctv'));
    }

    public function edit(Cctv $cctv)
    {
        return view('admin.cctvs.edit', compact('cctv'));
    }

    public function update(Request $request, Cctv $cctv)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'username' => 'nullable|string|max:100',
            'password' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'stream_url' => 'nullable|string|max:500',
            'snapshot_url' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive,error',
        ]);

        $cctv->update($validated);

        return redirect()->route('admin.cctvs.index')->with('success', 'CCTV berhasil diupdate');
    }

    public function destroy(Cctv $cctv)
    {
        $cctv->delete();
        return redirect()->route('admin.cctvs.index')->with('success', 'CCTV berhasil dihapus');
    }

    /**
     * Cek status online CCTV via ping.
     */
    public function ping(Cctv $cctv)
    {
        $ip = $cctv->ip_address;
        
        // Ping ke IP (Windows/Linux compatible)
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $pingCmd = "ping -n 1 -w 1000 {$ip} 2>&1";
        } else {
            $pingCmd = "ping -c 1 -W 1 {$ip} 2>&1";
        }
        
        exec($pingCmd, $output, $status);
        
        $isOnline = $status === 0;
        
        $cctv->update(['status' => $isOnline ? 'active' : 'error']);
        
        return response()->json([
            'online' => $isOnline,
            'ip' => $ip,
        ]);
    }

    /**
     * Ambil snapshot dari CCTV.
     */
    public function snapshot(Cctv $cctv)
    {
        $ip = $cctv->ip_address;
        $port = $cctv->port;
        $username = $cctv->username;
        $password = $cctv->password;

        $snapshotUrl = $cctv->snapshot_url ?: "http://{$ip}:{$port}/ISAPI/Streaming/channels/101/picture";

        // Definisikan client di luar
        $client = new \GuzzleHttp\Client([
            'timeout' => 5,
            'auth' => [$username, $password, 'digest'],
        ]);

        try {
            $response = $client->get($snapshotUrl);
            $imageData = $response->getBody()->getContents();

            return response($imageData)
                ->header('Content-Type', 'image/jpeg')
                ->header('Cache-Control', 'no-cache');
        } catch (\Exception $e) {
            // Fallback URL
            try {
                $fallbackUrl = "http://{$ip}:{$port}/ISAPI/Streaming/channels/1/picture";
                $response = $client->get($fallbackUrl);
                return response($response->getBody()->getContents())
                    ->header('Content-Type', 'image/jpeg');
            } catch (\Exception $e2) {
                return response()->json([
                    'error' => 'Gagal mengambil snapshot.'
                ], 500);
            }
        }
    }

    /**
     * Auto ping semua CCTV.
     */
    public function pingAll()
    {
        $cctvs = Cctv::all();
        $results = [];
        
        foreach ($cctvs as $cctv) {
            $ip = $cctv->ip_address;
            
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $pingCmd = "ping -n 1 -w 1000 {$ip} 2>&1";
            } else {
                $pingCmd = "ping -c 1 -W 1 {$ip} 2>&1";
            }
            
            exec($pingCmd, $output, $status);
            
            $isOnline = $status === 0;
            $cctv->update(['status' => $isOnline ? 'active' : 'error']);
            
            $results[] = [
                'id' => $cctv->id,
                'name' => $cctv->name,
                'ip' => $ip,
                'online' => $isOnline,
            ];
        }
        
        return response()->json($results);
    }

    /**
     * Show import form.
     */
    public function showImportForm()
    {
        return view('admin.cctvs.import');
    }

    /**
     * Download template.
     */
    public function downloadTemplate()
    {
        return Excel::download(new CctvTemplateExport, 'template_import_cctv.xlsx');
    }

    /**
     * Import from Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $import = new CctvsImport;
            Excel::import($import, $request->file('file'));

            return redirect()->route('admin.cctvs.index')
                ->with('success', 'Import berhasil!');
                
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            
            foreach ($failures as $failure) {
                $errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }

            return view('admin.cctvs.import', [
                'import_errors' => $errors,
                'import_total' => count($failures),
            ]);
                
        } catch (\Exception $e) {
            return view('admin.cctvs.import', [
                'import_errors' => [$e->getMessage()],
                'import_total' => 1,
            ]);
        }
    }
}
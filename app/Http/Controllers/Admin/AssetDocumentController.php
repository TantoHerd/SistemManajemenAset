<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class AssetDocumentController extends Controller
{
    public function store(Request $request, Asset $asset)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'name' => 'nullable|string|max:255',
            'file_type' => 'nullable|in:invoice,photo,manual,other',
            'folder_path' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        
        // Auto folder by kategori aset
        $folderPath = $request->folder_path;
        if (!$folderPath) {
            $folderPath = strtoupper(str_replace(' ', '-', $asset->category->name ?? 'Uncategorized'));
        }
        
        // Path: asset-documents/KATEGORI/{asset_id}-{nama_aset}/
        $storageFolder = 'asset-documents/' . $folderPath . '/' . $asset->id . '-' . str_replace(' ', '-', $asset->name);
        $path = $file->store($storageFolder, 'public');

        $asset->documents()->create([
            'name' => $request->name ?: $file->getClientOriginalName(),
            'file_path' => $path,
            'folder_path' => $folderPath,
            'file_type' => $request->file_type ?? 'other',
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Dokumen berhasil diupload');
    }

    public function download(AssetDocument $document)
    {
        return Storage::disk('public')->download($document->file_path, $document->name);
    }

    /**
     * Download semua dokumen dalam folder sebagai ZIP.
     */
    public function downloadFolder(Request $request, Asset $asset)
    {
        $folderPath = $request->folder_path;
        
        $query = $asset->documents();
        if ($folderPath) {
            $query->where('folder_path', $folderPath);
        }
        $documents = $query->get();

        if ($documents->isEmpty()) {
            return back()->with('error', 'Tidak ada dokumen');
        }

        // Buat ZIP
        $zipFileName = 'dokumen-' . str_replace(' ', '-', $asset->name) . '.zip';
        $zipPath = storage_path('app/public/temp/' . $zipFileName);
        
        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($documents as $doc) {
                $filePath = storage_path('app/public/' . $doc->file_path);
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $doc->name);
                }
            }
            $zip->close();
        }

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    public function destroy(AssetDocument $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Dokumen berhasil dihapus');
    }
}
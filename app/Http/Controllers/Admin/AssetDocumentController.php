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
    /**
     * Upload multiple documents (AJAX).
     */
    public function store(Request $request, Asset $asset)
    {
        $request->validate([
            'files.*' => 'required|file|max:10240',
            'file_type' => 'nullable|in:invoice,photo,manual,other',
            'folder_path' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $uploaded = [];
        $folderPath = $request->folder_path ?: strtoupper(str_replace(' ', '-', $asset->category->name ?? 'Uncategorized'));
        $storageFolder = 'asset-documents/' . $folderPath . '/' . $asset->id . '-' . str_replace(' ', '-', $asset->name);

        foreach ($request->file('files') as $file) {
            $path = $file->store($storageFolder, 'public');

            $doc = $asset->documents()->create([
                'name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'folder_path' => $folderPath,
                'file_type' => $request->file_type ?? 'other',
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'notes' => $request->notes,
            ]);

            $uploaded[] = [
                'id' => $doc->id,
                'name' => $doc->name,
                'icon' => $doc->file_icon,
                'size' => $doc->file_size_formatted,
                'url' => $doc->file_url,
                'download_url' => route('admin.documents.download', $doc),
                'delete_url' => route('admin.documents.destroy', $doc),
            ];
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'documents' => $uploaded]);
        }

        return back()->with('success', count($uploaded) . ' dokumen berhasil diupload');
    }

    /**
     * Download document.
     */
    public function download(AssetDocument $document)
    {
        return Storage::disk('public')->download($document->file_path, $document->name);
    }

    /**
     * Download all documents in a folder as ZIP.
     */
    public function downloadFolder(Request $request, Asset $asset)
    {
        $folderPath = $request->folder_path;
        
        $query = $asset->documents();
        if ($folderPath) $query->where('folder_path', $folderPath);
        $documents = $query->get();

        if ($documents->isEmpty()) return back()->with('error', 'Tidak ada dokumen');

        $zipFileName = 'dokumen-' . str_replace(' ', '-', $asset->name) . '.zip';
        $zipPath = storage_path('app/public/temp/' . $zipFileName);
        
        if (!file_exists(dirname($zipPath))) mkdir(dirname($zipPath), 0755, true);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($documents as $doc) {
                $filePath = storage_path('app/public/' . $doc->file_path);
                if (file_exists($filePath)) $zip->addFile($filePath, $doc->name);
            }
            $zip->close();
        }

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    /**
     * Delete document (AJAX).
     */
    public function destroy(AssetDocument $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Dokumen berhasil dihapus');
    }
}
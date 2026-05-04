<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class AssetDocument extends Model
{
    protected $fillable = [
        'asset_id', 'name', 'file_path', 'folder_path',
        'file_type', 'mime_type', 'file_size', 'notes'
    ];

    protected $appends = ['file_url', 'file_icon', 'file_size_formatted', 'folder_name'];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getFileIconAttribute(): string
    {
        $ext = pathinfo($this->file_path, PATHINFO_EXTENSION);
        
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) return 'bi-file-image';
        if ($ext === 'pdf') return 'bi-file-pdf';
        if (in_array($ext, ['doc', 'docx'])) return 'bi-file-word';
        if (in_array($ext, ['xls', 'xlsx'])) return 'bi-file-excel';
        
        return match ($this->file_type) {
            'invoice' => 'bi-receipt',
            'photo' => 'bi-camera',
            'manual' => 'bi-book',
            default => 'bi-file-earmark',
        };
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 1) . ' GB';
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    public function getFolderNameAttribute(): string
    {
        return $this->folder_path ?: 'Uncategorized';
    }

    public function isImage(): bool
    {
        return in_array(pathinfo($this->file_path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    protected static function booted(): void
    {
        static::deleting(function ($doc) {
            Storage::disk('public')->delete($doc->file_path);
        });
    }
}
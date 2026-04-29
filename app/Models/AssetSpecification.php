<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetSpecification extends Model
{
    protected $fillable = [
        'asset_id',
        'spec_key',
        'spec_value'
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    // Bulk set specifications
    public static function setSpecifications(Asset $asset, array $specs): void
    {
        foreach ($specs as $key => $value) {
            if ($value !== null && $value !== '') {
                $asset->specifications()->updateOrCreate(
                    ['spec_key' => $key],
                    ['spec_value' => $value]
                );
            } else {
                // Hapus spesifikasi jika nilainya kosong
                $asset->specifications()->where('spec_key', $key)->delete();
            }
        }
    }
}
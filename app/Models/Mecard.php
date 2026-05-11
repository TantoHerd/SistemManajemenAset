<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mecard extends Model
{
    protected $fillable = [
        'name', 'title', 'phone', 'email', 'company', 'address', 'note'
    ];

    /**
     * Generate MeCard string.
     */
    public function toMeCard(): string
    {
        $mecard = "MECARD:";
        $mecard .= "N:{$this->name};";
        if ($this->title) $mecard .= "TITLE:{$this->title};";
        if ($this->phone) $mecard .= "TEL:{$this->phone};";
        if ($this->email) $mecard .= "EMAIL:{$this->email};";
        if ($this->company) $mecard .= "ORG:{$this->company};";
        if ($this->address) $mecard .= "ADR:{$this->address};";
        if ($this->note) $mecard .= "NOTE:{$this->note};";
        $mecard .= ";";
        
        return $mecard;
    }
}
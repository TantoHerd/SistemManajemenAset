<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mecard extends Model
{
    use HasFactory;

    protected $table = 'mecards';

    protected $fillable = [
        'name', 'title', 'company',
        'phone', 'email', 'website', 'address',
        'phones', 'emails', 'addresses', 'socials', 'custom_fields',
        'note', 'logo_path'
    ];

    protected $casts = [
        'phones' => 'array',
        'emails' => 'array',
        'addresses' => 'array',
        'socials' => 'array',
        'custom_fields' => 'array',
    ];

    // ============ HELPER METHODS ============
    
    /**
     * Get all phone numbers
     */
    public function getAllPhones(): array
    {
        if ($this->phones && is_array($this->phones)) {
            return $this->phones;
        }
        
        // Fallback ke single phone
        if ($this->phone) {
            return [['type' => 'WORK', 'number' => $this->phone]];
        }
        
        return [];
    }
    
    /**
     * Get all emails
     */
    public function getAllEmails(): array
    {
        if ($this->emails && is_array($this->emails)) {
            return $this->emails;
        }
        
        // Fallback ke single email
        if ($this->email) {
            return [['type' => 'WORK', 'address' => $this->email]];
        }
        
        return [];
    }
    
    /**
     * Get all addresses
     */
    public function getAllAddresses(): array
    {
        if ($this->addresses && is_array($this->addresses)) {
            return $this->addresses;
        }
        
        // Fallback ke single address
        if ($this->address) {
            return [['type' => 'WORK', 'text' => $this->address]];
        }
        
        return [];
    }
    
    /**
     * Get all social media / websites
     */
    public function getAllSocials(): array
    {
        if ($this->socials && is_array($this->socials)) {
            return $this->socials;
        }
        
        // Fallback ke single website
        if ($this->website) {
            return [['type' => 'WEBSITE', 'url' => $this->website]];
        }
        
        return [];
    }
    
    /**
     * Get all custom fields
     */
    public function getAllCustomFields(): array
    {
        if ($this->custom_fields && is_array($this->custom_fields)) {
            return $this->custom_fields;
        }
        
        return [];
    }
    
    /**
     * Generate MECARD string for QR code
     */
    public function toMeCard(): string
    {
        $mecard = "MECARD:";
        $mecard .= "N:{$this->name};";
        
        // Batasi hanya data penting untuk QR yang rapi
        if ($this->phone) {
            $mecard .= "TEL:{$this->phone};";
        }
        
        if ($this->email) {
            $mecard .= "EMAIL:{$this->email};";
        }
        
        if ($this->company) {
            $mecard .= "ORG:{$this->company};";
        }
        
        if ($this->title) {
            $mecard .= "TITLE:{$this->title};";
        }
        
        if ($this->address) {
            $mecard .= "ADR:{$this->address};;;";
        }
        
        $mecard .= ";";
        
        return $mecard;
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmeOnboarding extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'programmes',
        'nursing_qualification',
        'pincode',
        'city',
        'state',
        'first_name',
        'last_name',
        'age_group',
        'gender',
        'highest_qualification',
        'vehicle',
        'driving_licence',
        'mobile',
        'is_whatsapp_same',
        'whatsapp_number',
        'email',
        'referral_source',
        'referral_name',
        'referral_contact',
        'referral_org',
        'confirmed_accuracy',
        'confirmed_conditions',
        'confirmed_contact',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'programmes' => 'array',
        'is_whatsapp_same' => 'boolean',
        'confirmed_accuracy' => 'boolean',
        'confirmed_conditions' => 'boolean',
        'confirmed_contact' => 'boolean',
    ];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getEffectiveWhatsappAttribute(): string
    {
        if ($this->is_whatsapp_same || empty($this->whatsapp_number)) {
            return $this->mobile;
        }
        return $this->whatsapp_number;
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'type', 'currency', 'country', 'contact_person',
        'email', 'phone', 'mobile', 'address',
        'ntn_number', 'strn_number',
        'credit_limit', 'lead_time_days', 'payment_terms',
        'bank_details', 'opening_balance', 'is_active',
    ];

    protected $casts = [
        'credit_limit'   => 'decimal:2',
        'opening_balance'=> 'decimal:2',
        'is_active'      => 'boolean',
    ];

    public function ledger()    { return $this->hasOne(VendorLedger::class); }
    public function purchases() { return $this->hasMany(Purchase::class); }

    public function isInternational(): bool { return $this->type === 'international'; }
    public function isLocal(): bool         { return $this->type === 'local'; }

    public function getTypeBadgeAttribute(): string
    {
        return $this->type === 'international'
            ? '<span class="badge bg-primary"><i class="fas fa-globe me-1"></i>International</span>'
            : '<span class="badge bg-success"><i class="fas fa-map-marker-alt me-1"></i>Local</span>';
    }
}

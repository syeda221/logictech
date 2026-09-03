<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequisitionItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'required_qty'         => 'decimal:3',
        'estimated_unit_price' => 'decimal:2',
        'ordered_qty'          => 'decimal:3',
    ];

    public function requisition() { return $this->belongsTo(PurchaseRequisition::class, 'pr_id'); }
    public function product()     { return $this->belongsTo(Product::class); }

    public function getPendingQtyAttribute(): float
    {
        return max(0, $this->required_qty - $this->ordered_qty);
    }
}

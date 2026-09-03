<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'price'               => 'decimal:2',
        'item_discount'       => 'decimal:2',
        'tax_percentage'      => 'decimal:2',
        'tax_amount'          => 'decimal:2',
        'foreign_unit_price'  => 'decimal:4',
        'line_total'          => 'decimal:2',
        'received_qty'        => 'decimal:3',
    ];

    public function purchase() { return $this->belongsTo(Purchase::class); }
    public function product()  { return $this->belongsTo(Product::class); }
    public function grnItems() { return $this->hasMany(GrnItem::class); }

    public function getPendingQtyAttribute(): float
    {
        return max(0, $this->qty - $this->received_qty);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrnItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'ordered_qty'   => 'decimal:3',
        'received_qty'  => 'decimal:3',
        'accepted_qty'  => 'decimal:3',
        'rejected_qty'  => 'decimal:3',
        'unit_price'    => 'decimal:2',
        'expiry_date'   => 'date',
        'debit_note_created' => 'boolean',
    ];

    public function grn()          { return $this->belongsTo(GrnReceipt::class, 'grn_id'); }
    public function product()      { return $this->belongsTo(Product::class); }
    public function warehouse()    { return $this->belongsTo(Warehouse::class); }
    public function purchaseItem() { return $this->belongsTo(PurchaseItem::class); }

    public function getLineTotalAttribute(): float
    {
        return round($this->accepted_qty * $this->unit_price, 2);
    }
}

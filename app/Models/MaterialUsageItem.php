<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialUsageItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'available_stock_at_time' => 'decimal:2',
        'qty_used' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function usage()
    {
        return $this->belongsTo(MaterialUsage::class, 'material_usage_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
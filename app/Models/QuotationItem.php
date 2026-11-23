<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    use HasFactory;

    protected $table = 'quotation_items';

    protected $fillable = [
        'quotation_id',
        'warehouse_id',
        'product_id',
        'product_name',
        'technical_name',
        'technical_specs',
        'technical_remarks',
        'brand_id',
        'brand_name',
        'category_id',
        'sub_category_id',
        'unit_id',
        'unit_name',
        'size_mode',
        'qty',
        'price',
        'discount_percent',
        'discount_amount',
        'tax_percent',
        'tax_amount',
        'total',
        'color',
        'model',
        'total_pieces',
        'loose_pieces',
        'price_per_piece',
        'is_manual',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'total_pieces' => 'integer',
        'loose_pieces' => 'integer',
        'price_per_piece' => 'decimal:2',
        'is_manual' => 'boolean',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }
}

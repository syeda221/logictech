<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcommerceOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'ecommerce_order_id',
        'product_id',
        'product_name',
        'price',
        'quantity',
        'size',
        'color',
        'total',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function order()
    {
        return $this->belongsTo(EcommerceOrder::class, 'ecommerce_order_id');
    }
}

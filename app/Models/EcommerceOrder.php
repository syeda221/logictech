<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcommerceOrder extends Model
{
    use HasFactory;

    public function customer()
    {
        return $this->belongsTo(WebCustomer::class, 'web_customer_id');
    }

    public function items()
    {
        return $this->hasMany(EcommerceOrderItem::class, 'ecommerce_order_id');
    }
}

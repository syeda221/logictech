<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class, 'creater_id');
    }
    public function products() {
        return $this->belongsToMany(Product::class, 'product_warehouse')
                    ->withPivot('stock');
    }

    public function getNameAttribute()
    {
        return $this->attributes['warehouse_name'] ?? ($this->attributes['name'] ?? 'Warehouse');
    }

    /**
     * Get default warehouse ID or auto-create a default warehouse if none exists
     */
    public static function getDefaultWarehouseId(): int
    {
        $warehouse = static::first();
        if (! $warehouse) {
            $warehouse = static::create([
                'warehouse_name' => 'Main Warehouse',
                'location' => 'Main Office',
            ]);
        }
        return (int) $warehouse->id;
    }
}

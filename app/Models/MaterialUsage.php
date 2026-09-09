<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialUsage extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'total_qty' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(MaterialUsageItem::class, 'material_usage_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public static function generateNextUsageNo(): string
    {
        $lastRecord = self::latest('id')->first();
        $nextId = $lastRecord ? ($lastRecord->id + 1) : 1;
        return 'MU-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }
}
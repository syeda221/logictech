<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairOrderLog extends Model
{
    use HasFactory;

    protected $table = 'repair_order_logs';

    protected $fillable = [
        'repair_order_id',
        'user_id',
        'action',
        'from_status',
        'to_status',
        'amount',
        'account_id',
        'notes',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function repairOrder()
    {
        return $this->belongsTo(RepairOrder::class, 'repair_order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}

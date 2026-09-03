<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DayClosing extends Model
{
    use HasFactory;

    protected $fillable = [
        'opening_balance',
        'inflow_amount',
        'outflow_amount',
        'expected_balance',
        'actual_balance',
        'difference',
        'opened_at',
        'closed_at',
        'status',
        'remarks',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'inflow_amount' => 'decimal:2',
        'outflow_amount' => 'decimal:2',
        'expected_balance' => 'decimal:2',
        'actual_balance' => 'decimal:2',
        'difference' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];
    public function drawerTransactions()
    {
        return $this->hasMany(DrawerTransaction::class, 'day_closing_id');
    }

    public function returnedTransactions()
    {
        return $this->hasMany(DrawerTransaction::class, 'returned_in_closing_id');
    }
}

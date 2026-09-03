<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DrawerTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_closing_id',
        'type',
        'category',
        'amount',
        'description',
        'status',
        'returned_in_closing_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function dayClosing()
    {
        return $this->belongsTo(DayClosing::class, 'day_closing_id');
    }

    public function returnClosing()
    {
        return $this->belongsTo(DayClosing::class, 'returned_in_closing_id');
    }
}

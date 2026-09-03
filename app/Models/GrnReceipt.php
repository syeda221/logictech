<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrnReceipt extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'received_date'   => 'date',
        'stock_updated'   => 'boolean',
    ];

    const STATUS_DRAFT     = 'draft';
    const STATUS_CONFIRMED = 'confirmed';

    const QC_PENDING  = 'pending';
    const QC_PASSED   = 'passed';
    const QC_FAILED   = 'failed';
    const QC_PARTIAL  = 'partial';

    public function purchase()      { return $this->belongsTo(Purchase::class); }
    public function items()         { return $this->hasMany(GrnItem::class, 'grn_id'); }
    public function receiver()      { return $this->belongsTo(User::class, 'received_by'); }
    public function storeIncharge() { return $this->belongsTo(User::class, 'store_incharge_id'); }

    public function getTotalReceivedQtyAttribute(): float
    {
        return $this->items->sum('received_qty');
    }

    public function getTotalAcceptedQtyAttribute(): float
    {
        return $this->items->sum('accepted_qty');
    }

    public function getTotalRejectedQtyAttribute(): float
    {
        return $this->items->sum('rejected_qty');
    }

    public function getQcStatusBadgeAttribute(): string
    {
        return match($this->qc_status) {
            'pending' => '<span class="badge bg-warning text-dark">QC Pending</span>',
            'passed'  => '<span class="badge bg-success">QC Passed</span>',
            'failed'  => '<span class="badge bg-danger">QC Failed</span>',
            'partial' => '<span class="badge bg-info">Partial Pass</span>',
            default   => '<span class="badge bg-secondary">' . ucfirst($this->qc_status) . '</span>',
        };
    }

    public static function generateGrnNumber(): string
    {
        $last = static::withTrashed()->latest('id')->first();
        $next = $last ? ($last->id + 1) : 1;
        return 'GRN-' . date('Y') . '-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}

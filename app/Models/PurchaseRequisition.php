<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequisition extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'required_by_date' => 'date',
        'approved_at' => 'datetime',
    ];

    const STATUS_DRAFT            = 'draft';
    const STATUS_PENDING          = 'pending_approval';
    const STATUS_APPROVED         = 'approved';
    const STATUS_REJECTED         = 'rejected';
    const STATUS_PARTIALLY_ORDERED= 'partially_ordered';
    const STATUS_CLOSED           = 'closed';

    public function branch()     { return $this->belongsTo(Branch::class); }
    public function warehouse()  { return $this->belongsTo(Warehouse::class); }
    public function requester()  { return $this->belongsTo(User::class, 'requested_by'); }
    public function approver()   { return $this->belongsTo(User::class, 'approved_by'); }
    public function items()      { return $this->hasMany(PurchaseRequisitionItem::class, 'pr_id'); }
    public function purchaseOrders() { return $this->hasMany(Purchase::class, 'pr_id'); }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'draft'             => '<span class="badge bg-secondary">Draft</span>',
            'pending_approval'  => '<span class="badge bg-warning text-dark">Pending Approval</span>',
            'approved'          => '<span class="badge bg-success">Approved</span>',
            'rejected'          => '<span class="badge bg-danger">Rejected</span>',
            'partially_ordered' => '<span class="badge bg-info">Partially Ordered</span>',
            'closed'            => '<span class="badge bg-dark">Closed</span>',
            default             => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>',
        };
    }

    public static function generatePrNumber(): string
    {
        $last = static::withTrashed()->latest('id')->first();
        $next = $last ? ($last->id + 1) : 1;
        return 'PR-' . date('Y') . '-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}

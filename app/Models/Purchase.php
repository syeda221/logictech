<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'purchase_date'         => 'date',
        'expected_delivery_date'=> 'date',
        'actual_arrival_date'   => 'date',
        'clearance_date'        => 'date',
        'subtotal'              => 'decimal:2',
        'discount'              => 'decimal:2',
        'additional_discount'   => 'decimal:2',
        'extra_cost'            => 'decimal:2',
        'net_amount'            => 'decimal:2',
        'paid_amount'           => 'decimal:2',
        'due_amount'            => 'decimal:2',
        'exchange_rate'         => 'decimal:6',
    ];

    // PO Status constants
    const PO_PENDING            = 'pending';
    const PO_APPROVED           = 'approved';
    const PO_IN_TRANSIT         = 'in_transit';
    const PO_PARTIALLY_RECEIVED = 'partially_received';
    const PO_RECEIVED           = 'received';
    const PO_PARTIALLY_COMPLETE = 'partially_complete';
    const PO_COMPLETE           = 'complete';
    const PO_CANCELLED          = 'cancelled';

    public function branch()        { return $this->belongsTo(Branch::class); }
    public function warehouse()     { return $this->belongsTo(Warehouse::class); }
    public function vendor()        { return $this->belongsTo(Vendor::class, 'vendor_id'); }
    public function items()         { return $this->hasMany(PurchaseItem::class); }
    public function returns()       { return $this->hasMany(PurchaseReturn::class); }
    public function grns()          { return $this->hasMany(GrnReceipt::class); }
    public function shipmentCosts() { return $this->hasOne(ImportShipmentCost::class); }
    public function requisition()   { return $this->belongsTo(PurchaseRequisition::class, 'pr_id'); }
    public function createdBy()     { return $this->belongsTo(User::class, 'created_by'); }

    public function isImport(): bool { return $this->purchase_type === 'import'; }
    public function isLocal(): bool  { return $this->purchase_type === 'local'; }

    public function getTotalOrderedQty(): float
    {
        return $this->items->sum('qty');
    }

    public function getTotalReceivedQty(): float
    {
        return $this->grns->flatMap->items->sum('accepted_qty');
    }

    /**
     * Compute Landed Cost per unit for this import PO
     * Landed Cost = Unit Cost (PKR) + (Total Shipment Extra Costs / Total Qty)
     */
    public function getLandedCostPerUnit(): float
    {
        $shipmentCost = $this->shipmentCosts;
        if (!$shipmentCost) return 0;

        $totalQty = $this->getTotalOrderedQty();
        if ($totalQty <= 0) return 0;

        $avgUnitCostPkr = $this->subtotal / $totalQty;
        return $shipmentCost->computeLandedCostPerUnit($totalQty, $avgUnitCostPkr);
    }

    public function getPoStatusBadgeAttribute(): string
    {
        return match($this->po_status) {
            'pending'            => '<span class="badge" style="background:#f59e0b;color:#fff;"><i class="fas fa-clock me-1"></i>Pending</span>',
            'approved'           => '<span class="badge bg-primary"><i class="fas fa-check me-1"></i>Approved</span>',
            'in_transit'         => '<span class="badge" style="background:#3b82f6;color:#fff;"><i class="fas fa-ship me-1"></i>In Transit</span>',
            'partially_received' => '<span class="badge" style="background:#8b5cf6;color:#fff;"><i class="fas fa-box-open me-1"></i>Partially Received</span>',
            'received'           => '<span class="badge bg-info"><i class="fas fa-warehouse me-1"></i>Received</span>',
            'partially_complete' => '<span class="badge" style="background:#f97316;color:#fff;"><i class="fas fa-undo me-1"></i>Partially Complete</span>',
            'complete'           => '<span class="badge bg-success"><i class="fas fa-check-double me-1"></i>Complete</span>',
            'cancelled'          => '<span class="badge bg-danger"><i class="fas fa-times me-1"></i>Cancelled</span>',
            default              => '<span class="badge bg-secondary">' . ucfirst($this->po_status) . '</span>',
        };
    }

    /** Auto-compute PO status based on GRN + returns */
    public function recomputePoStatus(): string
    {
        $totalOrdered  = $this->getTotalOrderedQty();
        $totalReceived = $this->getTotalReceivedQty();
        $totalReturned = $this->returns->sum('total_qty') ?? 0;

        if ($totalOrdered <= 0) return self::PO_PENDING;

        if ($totalReceived <= 0) return self::PO_PENDING;

        if ($totalReturned > 0 && $totalReceived >= $totalOrdered) {
            return self::PO_PARTIALLY_COMPLETE;
        }

        if ($totalReturned == 0 && $totalReceived >= $totalOrdered) {
            return self::PO_COMPLETE;
        }

        if ($totalReceived > 0 && $totalReceived < $totalOrdered) {
            return self::PO_PARTIALLY_RECEIVED;
        }

        return self::PO_PENDING;
    }
}

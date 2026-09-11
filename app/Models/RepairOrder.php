<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairOrder extends Model
{
    use HasFactory;

    protected $table = 'repair_orders';

    protected $fillable = [
        'repair_no',
        'customer_id',
        'customer_name',
        'customer_phone',
        'customer_address',
        'product_id',
        'item_name',
        'brand_model',
        'serial_no',
        'accessories_received',
        'problem_description',
        'physical_condition',
        'technician_notes',
        'estimated_cost',
        'service_charges',
        'parts_charges',
        'total_charges',
        'advance_paid',
        'advance_account_id',
        'final_paid',
        'final_account_id',
        'due_amount',
        'status',
        'priority',
        'received_date',
        'expected_delivery_date',
        'delivered_at',
        'received_by',
        'delivered_by',
    ];

    protected $casts = [
        'received_date' => 'date',
        'expected_delivery_date' => 'date',
        'delivered_at' => 'datetime',
        'estimated_cost' => 'float',
        'service_charges' => 'float',
        'parts_charges' => 'float',
        'total_charges' => 'float',
        'advance_paid' => 'float',
        'final_paid' => 'float',
        'due_amount' => 'float',
    ];

    // Customer Relationship
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    // Optional Finished Goods Catalog Product Relationship
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Advance Payment Account
    public function advanceAccount()
    {
        return $this->belongsTo(Account::class, 'advance_account_id');
    }

    // Final Payment Account
    public function finalAccount()
    {
        return $this->belongsTo(Account::class, 'final_account_id');
    }

    // Staff who received
    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    // Staff who delivered
    public function deliverer()
    {
        return $this->belongsTo(User::class, 'delivered_by');
    }

    // Audit logs
    public function logs()
    {
        return $this->hasMany(RepairOrderLog::class, 'repair_order_id')->latest();
    }

    /**
     * Get display customer name
     */
    public function getCustomerDisplayNameAttribute()
    {
        return $this->customer->customer_name ?? $this->customer_name ?? 'Walk-in Customer';
    }

    /**
     * Get display customer phone
     */
    public function getCustomerDisplayPhoneAttribute()
    {
        return $this->customer->mobile ?? $this->customer_phone ?? '-';
    }

    /**
     * Status color helper
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'received' => 'badge-received',
            'diagnosing' => 'badge-diagnosing',
            'in_progress' => 'badge-in-progress',
            'waiting_parts' => 'badge-waiting-parts',
            'completed' => 'badge-completed',
            'delivered' => 'badge-delivered',
            'cancelled' => 'badge-cancelled',
            default => 'badge-secondary',
        };
    }

    /**
     * Status label helper
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'received' => 'Received',
            'diagnosing' => 'Diagnosing',
            'in_progress' => 'In Progress',
            'waiting_parts' => 'Waiting for Parts',
            'completed' => 'Ready / Completed',
            'delivered' => 'Delivered & Closed',
            'cancelled' => 'Cancelled',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }
}

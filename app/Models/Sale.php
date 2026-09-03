<?php

// app/Models/Sale.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'customer_id', 'reference', 'total_amount_Words', 'total_bill_amount',
        'total_extradiscount', 'total_net', 'cash', 'card', 'change',
        'total_items', 'discount_type', 'sale_status', 'invoice_no', 'is_booking'
    ];

    public function customer_relation()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function product_relation()
    {
        return $this->belongsTo(Product::class, 'product', 'id');
    }

    public static function generateInvoiceNo($prefix = null)
    {
        return \App\Models\InvoiceSeries::generateNextNo($prefix);
    }

    public static function resolveByIdOrInvoice($id, array $with = [])
    {
        $query = self::query();
        if (!empty($with)) {
            $query->with($with);
        }

        // 1. Exact ID match (if numeric)
        if (is_numeric($id)) {
            $sale = (clone $query)->find((int)$id);
            if ($sale) {
                return $sale;
            }
        }

        // 2. Exact invoice_no match
        $sale = (clone $query)->where('invoice_no', $id)->first();
        if ($sale) {
            return $sale;
        }

        // 3. Normalized / padded invoice match
        $numericOnly = preg_replace('/[^0-9]/', '', (string)$id);
        if (!empty($numericOnly)) {
            $padded4 = 'INV-' . str_pad((int)$numericOnly, 4, '0', STR_PAD_LEFT);
            $padded5 = 'INV-' . str_pad((int)$numericOnly, 5, '0', STR_PAD_LEFT);
            $sale = (clone $query)->whereIn('invoice_no', [$padded4, $padded5, 'INV-' . $numericOnly])
                ->orWhere('invoice_no', 'LIKE', '%-' . $numericOnly)
                ->first();
            if ($sale) {
                return $sale;
            }
        }

        return null;
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function journalEntries()
    {
        return $this->morphMany(JournalEntry::class, 'source');
    }

    public function returns()
    {
        return $this->hasMany(SaleReturn::class, 'sale_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
            if (!isset($model->is_synced)) {
                $model->is_synced = 0;
            }
        });
    }
}

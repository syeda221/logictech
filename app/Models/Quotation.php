<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $table = 'quotations';

    protected $fillable = [
        'quote_no',
        'customer_id',
        'walkin_name',
        'reference',
        'quote_title',
        'quotation_date',
        'estimated_delivery_date',
        'total_bill_amount',
        'total_discount',
        'tax_percent',
        'tax_amount',
        'total_net',
        'total_items',
        'terms_and_conditions',
        'status',
        'converted_sale_id',
        'converted_at',
        'created_by',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'estimated_delivery_date' => 'date',
        'total_bill_amount' => 'decimal:2',
        'total_discount' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_net' => 'decimal:2',
        'converted_at' => 'datetime',
    ];

    public const DEFAULT_TERMS = "• validity: The quoted prices are valid for 15 days.\n• Terms of Payments: 50% advance at the time of order & remaining 50% at the time of delivery.\n• Delivery Time: 15-20 Working Days after receiving 50% advanced payment.\n• Warranty: 01-year warranty. (Free Service Warranty only).\n• Delivery Terms: The buyer is responsible for:\n  • Arranging pick-up of the goods from the factory\n  • Paying all transportation costs\n  • Bearing the risks for bringing the goods to their final destination";

    public function customer_relation()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class, 'quotation_id', 'id');
    }

    public function convertedSale()
    {
        return $this->belongsTo(Sale::class, 'converted_sale_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public static function generateQuoteNo($prefix = 'QUO')
    {
        $prefix = strtoupper(trim($prefix ?: 'QUO'));
        $series = InvoiceSeries::where('prefix', $prefix)->first();
        $padding = $series ? ($series->padding ?: 4) : 4;

        $lastQuote = self::where('quote_no', 'LIKE', $prefix . '-%')
            ->orderBy('id', 'desc')
            ->first();

        $numFromQuote = 0;
        if ($lastQuote && $lastQuote->quote_no) {
            if (preg_match('/' . preg_quote($prefix, '/') . '-(\d+)/i', $lastQuote->quote_no, $matches)) {
                $numFromQuote = (int) $matches[1];
            }
        }

        $seriesNext = $series ? (int)$series->next_number : 1;
        $nextNum = max($seriesNext, $numFromQuote + 1);

        return $prefix . '-' . str_pad($nextNum, $padding, '0', STR_PAD_LEFT);
    }

    public static function incrementCounterForQuote($quoteNo)
    {
        if (empty($quoteNo)) return;

        if (preg_match('/^([A-Z0-9]+)-(\d+)$/i', trim($quoteNo), $matches)) {
            $prefix = strtoupper($matches[1]);
            $number = (int) $matches[2];

            $series = InvoiceSeries::where('prefix', $prefix)->first();
            if ($series) {
                if ($number >= $series->next_number) {
                    $series->next_number = $number + 1;
                    $series->save();
                }
            } else {
                InvoiceSeries::create([
                    'prefix' => $prefix,
                    'next_number' => $number + 1,
                    'padding' => strlen($matches[2]),
                    'is_default' => 0,
                ]);
            }
        }
    }

    public static function resolveByIdOrQuoteNo($id, array $with = [])
    {
        $query = self::query();
        if (!empty($with)) {
            $query->with($with);
        }

        if (is_numeric($id)) {
            $quote = (clone $query)->find((int)$id);
            if ($quote) {
                return $quote;
            }
        }

        $quote = (clone $query)->where('quote_no', $id)->first();
        if ($quote) {
            return $quote;
        }

        $numericOnly = preg_replace('/[^0-9]/', '', (string)$id);
        if (!empty($numericOnly)) {
            $padded4 = 'QUO-' . str_pad((int)$numericOnly, 4, '0', STR_PAD_LEFT);
            $padded5 = 'QUO-' . str_pad((int)$numericOnly, 5, '0', STR_PAD_LEFT);
            $quote = (clone $query)->whereIn('quote_no', [$padded4, $padded5, 'QUO-' . $numericOnly])
                ->orWhere('quote_no', 'LIKE', '%-' . $numericOnly)
                ->first();
            if ($quote) {
                return $quote;
            }
        }

        return null;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportShipmentCost extends Model
{
    protected $guarded = [];

    protected $casts = [
        'freight_charges'       => 'decimal:2',
        'insurance_amount'      => 'decimal:2',
        'port_charges'          => 'decimal:2',
        'customs_duty'          => 'decimal:2',
        'regulatory_duty'       => 'decimal:2',
        'sales_tax_import'      => 'decimal:2',
        'clearing_agent_charges'=> 'decimal:2',
        'inland_freight'        => 'decimal:2',
        'misc_costs'            => 'decimal:2',
        'total_extra_cost'      => 'decimal:2',
        'landed_cost_per_unit'  => 'decimal:4',
    ];

    public function purchase() { return $this->belongsTo(Purchase::class); }

    /**
     * Calculate total of all extra logistic costs
     */
    public function computeTotal(): float
    {
        return round(
            $this->freight_charges +
            $this->insurance_amount +
            $this->port_charges +
            $this->customs_duty +
            $this->regulatory_duty +
            $this->sales_tax_import +
            $this->clearing_agent_charges +
            $this->inland_freight +
            $this->misc_costs,
            2
        );
    }

    /**
     * Landed Cost Per Unit = Unit Cost (PKR) + (Total Extra Costs / Total Qty)
     */
    public function computeLandedCostPerUnit(float $totalQty, float $avgUnitCostPkr = 0): float
    {
        if ($totalQty <= 0) return 0;
        $extraPerUnit = round($this->total_extra_cost / $totalQty, 4);
        return round($avgUnitCostPkr + $extraPerUnit, 4);
    }
}

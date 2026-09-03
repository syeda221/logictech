<?php

namespace App\Services;

use App\Models\ImportShipmentCost;
use App\Models\Purchase;
use App\Models\GrnItem;

class LandedCostService
{
    /**
     * Calculate and save landed cost for an import PO.
     * Formula: Landed Cost Per Unit = Avg Unit Cost (PKR) + (Total Extra Logistics / Total Shipped Qty)
     */
    public function calculate(Purchase $po, array $costData): ImportShipmentCost
    {
        $cost = ImportShipmentCost::updateOrCreate(
            ['purchase_id' => $po->id],
            [
                'freight_charges'        => $costData['freight_charges']        ?? 0,
                'insurance_amount'       => $costData['insurance_amount']       ?? 0,
                'port_charges'           => $costData['port_charges']           ?? 0,
                'customs_duty'           => $costData['customs_duty']           ?? 0,
                'regulatory_duty'        => $costData['regulatory_duty']        ?? 0,
                'sales_tax_import'       => $costData['sales_tax_import']       ?? 0,
                'clearing_agent_charges' => $costData['clearing_agent_charges'] ?? 0,
                'inland_freight'         => $costData['inland_freight']         ?? 0,
                'misc_costs'             => $costData['misc_costs']             ?? 0,
                'allocation_method'      => $costData['allocation_method']      ?? 'qty',
                'notes'                  => $costData['notes']                  ?? null,
            ]
        );

        // Compute total
        $cost->total_extra_cost = $cost->computeTotal();

        // Compute total qty from PO items
        $totalQty = $po->items->sum('qty');
        $avgUnitCostPkr = $totalQty > 0 ? ($po->subtotal / $totalQty) : 0;

        $cost->landed_cost_per_unit = $cost->computeLandedCostPerUnit($totalQty, $avgUnitCostPkr);
        $cost->save();

        return $cost;
    }

    /**
     * Get detailed breakdown for display
     */
    public function getBreakdown(Purchase $po): array
    {
        $cost = $po->shipmentCosts;
        $items = $po->items;
        $totalQty = $items->sum('qty');
        $subtotal = $po->subtotal;
        $avgUnitCost = $totalQty > 0 ? $subtotal / $totalQty : 0;

        if (!$cost) {
            return [
                'has_costs'             => false,
                'total_item_cost_pkr'   => round($subtotal, 2),
                'total_extra_cost'      => 0,
                'total_qty'             => $totalQty,
                'avg_unit_cost_pkr'     => round($avgUnitCost, 2),
                'extra_per_unit'        => 0,
                'landed_cost_per_unit'  => round($avgUnitCost, 2),
            ];
        }

        $extraPerUnit = $totalQty > 0 ? $cost->total_extra_cost / $totalQty : 0;

        return [
            'has_costs'             => true,
            'total_item_cost_pkr'   => round($subtotal, 2),
            'freight_charges'       => $cost->freight_charges,
            'insurance_amount'      => $cost->insurance_amount,
            'port_charges'          => $cost->port_charges,
            'customs_duty'          => $cost->customs_duty,
            'regulatory_duty'       => $cost->regulatory_duty,
            'sales_tax_import'      => $cost->sales_tax_import,
            'clearing_agent_charges'=> $cost->clearing_agent_charges,
            'inland_freight'        => $cost->inland_freight,
            'misc_costs'            => $cost->misc_costs,
            'total_extra_cost'      => $cost->total_extra_cost,
            'total_qty'             => $totalQty,
            'avg_unit_cost_pkr'     => round($avgUnitCost, 2),
            'extra_per_unit'        => round($extraPerUnit, 4),
            'landed_cost_per_unit'  => $cost->landed_cost_per_unit,
            'allocation_method'     => $cost->allocation_method,
        ];
    }
}

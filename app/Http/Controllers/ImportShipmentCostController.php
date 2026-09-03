<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\ImportShipmentCost;
use App\Services\LandedCostService;
use Illuminate\Http\Request;

class ImportShipmentCostController extends Controller
{
    protected LandedCostService $landedCostService;

    public function __construct(LandedCostService $landedCostService)
    {
        $this->landedCostService = $landedCostService;
    }

    /**
     * Show landed cost form for an Import PO
     */
    public function show($purchaseId)
    {
        $po = Purchase::with(['items.product', 'vendor', 'shipmentCosts'])->findOrFail($purchaseId);

        if (!$po->isImport()) {
            return back()->with('error', 'Landed Cost calculation is only for Import POs.');
        }

        $breakdown = $this->landedCostService->getBreakdown($po);
        $cost = $po->shipmentCosts;

        return view('admin_panel.purchase.landed_cost', compact('po', 'cost', 'breakdown'));
    }

    /**
     * Save / update shipment costs and recalculate landed cost
     */
    public function store(Request $request, $purchaseId)
    {
        $request->validate([
            'freight_charges'        => 'nullable|numeric|min:0',
            'insurance_amount'       => 'nullable|numeric|min:0',
            'port_charges'           => 'nullable|numeric|min:0',
            'customs_duty'           => 'nullable|numeric|min:0',
            'regulatory_duty'        => 'nullable|numeric|min:0',
            'sales_tax_import'       => 'nullable|numeric|min:0',
            'clearing_agent_charges' => 'nullable|numeric|min:0',
            'inland_freight'         => 'nullable|numeric|min:0',
            'misc_costs'             => 'nullable|numeric|min:0',
            'allocation_method'      => 'nullable|in:qty,weight,value',
        ]);

        $po = Purchase::with('items')->findOrFail($purchaseId);

        $cost = $this->landedCostService->calculate($po, $request->all());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'              => true,
                'total_extra_cost'     => $cost->total_extra_cost,
                'landed_cost_per_unit' => $cost->landed_cost_per_unit,
                'breakdown'            => $this->landedCostService->getBreakdown($po),
            ]);
        }

        return back()->with('success', 'Shipment costs saved. Landed Cost per unit: PKR ' . number_format($cost->landed_cost_per_unit, 2));
    }

    /**
     * AJAX: live calculate without saving
     */
    public function calculate(Request $request, $purchaseId)
    {
        $po = Purchase::with('items')->findOrFail($purchaseId);

        $freightCharges        = floatval($request->freight_charges        ?? 0);
        $insuranceAmount       = floatval($request->insurance_amount       ?? 0);
        $portCharges           = floatval($request->port_charges           ?? 0);
        $customsDuty           = floatval($request->customs_duty           ?? 0);
        $regulatoryDuty        = floatval($request->regulatory_duty        ?? 0);
        $salesTaxImport        = floatval($request->sales_tax_import       ?? 0);
        $clearingAgentCharges  = floatval($request->clearing_agent_charges ?? 0);
        $inlandFreight         = floatval($request->inland_freight         ?? 0);
        $miscCosts             = floatval($request->misc_costs             ?? 0);

        $totalExtra = $freightCharges + $insuranceAmount + $portCharges + $customsDuty
                    + $regulatoryDuty + $salesTaxImport + $clearingAgentCharges
                    + $inlandFreight + $miscCosts;

        $totalQty       = $po->items->sum('qty');
        $subtotal       = $po->subtotal;
        $avgUnitCostPkr = $totalQty > 0 ? $subtotal / $totalQty : 0;
        $extraPerUnit   = $totalQty > 0 ? $totalExtra / $totalQty : 0;
        $landedCost     = $avgUnitCostPkr + $extraPerUnit;

        return response()->json([
            'total_extra_cost'     => round($totalExtra, 2),
            'total_qty'            => $totalQty,
            'avg_unit_cost_pkr'    => round($avgUnitCostPkr, 2),
            'extra_per_unit'       => round($extraPerUnit, 4),
            'landed_cost_per_unit' => round($landedCost, 4),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\GrnItem;
use App\Models\GrnReceipt;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GrnController extends Controller
{
    /**
     * List all GRNs
     */
    public function index(Request $request)
    {
        $query = GrnReceipt::with(['purchase.vendor', 'receiver'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('qc_status')) {
            $query->where('qc_status', $request->qc_status);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('received_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('received_date', '<=', $request->to_date);
        }
        if ($request->filled('search')) {
            $query->where('grn_number', 'like', '%' . $request->search . '%');
        }

        $grns = $query->paginate(20);

        $stats = [
            'total'        => GrnReceipt::count(),
            'qc_pending'   => GrnReceipt::where('qc_status', 'pending')->count(),
            'qc_passed'    => GrnReceipt::where('qc_status', 'passed')->count(),
            'has_rejected' => GrnReceipt::whereHas('items', fn($q) => $q->where('rejected_qty', '>', 0))->count(),
        ];

        return view('admin_panel.grn.index', compact('grns', 'stats'));
    }

    /**
     * Show GRN create form (loaded from a PO)
     */
    public function create(Request $request)
    {
        $purchaseId = $request->get('purchase_id');
        $purchase   = null;

        if ($purchaseId) {
            $purchase = Purchase::with(['vendor', 'items.product', 'warehouse', 'branch'])
                ->findOrFail($purchaseId);
        }

        $pendingPOs = Purchase::with('vendor')
            ->whereNotIn('po_status', ['complete', 'cancelled'])
            ->latest()
            ->get();

        return view('admin_panel.grn.create', compact('purchase', 'pendingPOs'));
    }

    /**
     * Store new GRN and IMMEDIATELY update stock for accepted qty
     */
    public function store(Request $request)
    {
        $request->validate([
            'purchase_id'          => 'required|exists:purchases,id',
            'received_date'        => 'required|date',
            'delivery_challan_no'  => 'nullable|string',
            'qc_notes'             => 'nullable|string',
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|exists:products,id',
            'items.*.warehouse_id' => 'required|exists:warehouses,id',
            'items.*.received_qty' => 'required|numeric|min:0',
            'items.*.accepted_qty' => 'required|numeric|min:0',
            'items.*.rejected_qty' => 'required|numeric|min:0',
        ]);

        $hasRejected = false;
        foreach ($request->items as $item) {
            if (($item['rejected_qty'] ?? 0) > 0) {
                $hasRejected = true;
                break;
            }
        }

        $grn = DB::transaction(function () use ($request) {
            $purchase = Purchase::findOrFail($request->purchase_id);

            // Determine QC status
            $hasRejected = false;
            $allRejected = true;
            foreach ($request->items as $item) {
                if (($item['rejected_qty'] ?? 0) > 0) $hasRejected = true;
                if (($item['accepted_qty'] ?? 0) > 0) $allRejected = false;
            }
            $qcStatus = $allRejected ? 'failed' : ($hasRejected ? 'partial' : 'passed');

            $grn = GrnReceipt::create([
                'grn_number'          => GrnReceipt::generateGrnNumber(),
                'purchase_id'         => $request->purchase_id,
                'received_date'       => $request->received_date,
                'delivery_challan_no' => $request->delivery_challan_no,
                'received_by'         => Auth::id(),
                'store_incharge_id'   => Auth::id(),
                'qc_status'           => $qcStatus,
                'qc_notes'            => $request->qc_notes,
                'status'              => 'confirmed',
                'stock_updated'       => true,
                'notes'               => $request->notes,
            ]);

            foreach ($request->items as $item) {
                $acceptedQty = floatval($item['accepted_qty'] ?? 0);
                $rejectedQty = floatval($item['rejected_qty'] ?? 0);

                $grnItem = GrnItem::create([
                    'grn_id'           => $grn->id,
                    'purchase_item_id' => $item['purchase_item_id'] ?? null,
                    'product_id'       => $item['product_id'],
                    'warehouse_id'     => $item['warehouse_id'],
                    'ordered_qty'      => $item['ordered_qty'] ?? 0,
                    'received_qty'     => $item['received_qty'] ?? 0,
                    'accepted_qty'     => $acceptedQty,
                    'rejected_qty'     => $rejectedQty,
                    'rejection_reason' => $item['rejection_reason'] ?? null,
                    'batch_no'         => $item['batch_no'] ?? null,
                    'expiry_date'      => $item['expiry_date'] ?? null,
                    'unit_price'       => $item['unit_price'] ?? 0,
                ]);

                // IMMEDIATELY add accepted qty to warehouse stock
                if ($acceptedQty > 0) {
                    $this->updateWarehouseStock($item['product_id'], $item['warehouse_id'], $acceptedQty);
                    // Also update purchase_item received_qty
                    if (!empty($item['purchase_item_id'])) {
                        \App\Models\PurchaseItem::where('id', $item['purchase_item_id'])
                            ->increment('received_qty', $acceptedQty);
                    }
                }
            }

            // Update PO received qty and recompute status
            $purchase->load('items', 'grns.items', 'returns');
            $newStatus = $purchase->recomputePoStatus();
            $purchase->update(['po_status' => $newStatus]);

            return $grn;
        });

        // Check if any items were rejected - prompt for debit note
        if ($hasRejected) {
            return redirect()->route('grn.show', $grn->id)
                ->with('success', 'GRN created and stock updated successfully.')
                ->with('show_debit_note_modal', true)
                ->with('grn_id', $grn->id);
        }

        return redirect()->route('grn.show', $grn->id)
            ->with('success', 'GRN created and stock updated successfully.');
    }

    /**
     * Show a single GRN
     */
    public function show($id)
    {
        $grn = GrnReceipt::with(['purchase.vendor', 'items.product', 'items.warehouse', 'receiver'])
            ->findOrFail($id);
        return view('admin_panel.grn.show', compact('grn'));
    }

    /**
     * QC Reject action: immediately deduct stock for rejected qty
     */
    public function qcReject(Request $request, $id)
    {
        $request->validate([
            'grn_item_id'      => 'required|exists:grn_items,id',
            'rejection_reason' => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            $grnItem = GrnItem::findOrFail($request->grn_item_id);

            // Deduct stock immediately
            if ($grnItem->accepted_qty > 0) {
                $this->updateWarehouseStock($grnItem->product_id, $grnItem->warehouse_id, -$grnItem->accepted_qty);
            }

            // Move to rejected
            $grnItem->update([
                'rejected_qty'     => $grnItem->accepted_qty,
                'accepted_qty'     => 0,
                'rejection_reason' => $request->rejection_reason,
            ]);

            // Update GRN qc_status
            $grn = $grnItem->grn;
            $grn->update(['qc_status' => 'partial']);
        });

        return back()->with('success', 'QC rejection processed and stock deducted.')
            ->with('show_debit_note_modal', true);
    }

    /**
     * Create debit note for rejected items (manual or auto)
     */
    public function createDebitNote(Request $request, $grnId)
    {
        $request->validate([
            'mode'        => 'required|in:manual,auto',
            'grn_item_id' => 'required|exists:grn_items,id',
        ]);

        $grnItem = GrnItem::with(['grn.purchase', 'product'])->findOrFail($request->grn_item_id);
        $purchase = $grnItem->grn->purchase;

        if ($grnItem->debit_note_created) {
            return back()->with('error', 'Debit note already created for this item.');
        }

        if ($request->mode === 'auto') {
            DB::transaction(function () use ($grnItem, $purchase) {
                // Auto-create purchase return (debit note)
                $return = PurchaseReturn::create([
                    'purchase_id'   => $purchase->id,
                    'vendor_id'     => $purchase->vendor_id,
                    'return_date'   => now()->toDateString(),
                    'total_qty'     => $grnItem->rejected_qty,
                    'total_amount'  => round($grnItem->rejected_qty * $grnItem->unit_price, 2),
                    'note'          => 'Auto Debit Note - GRN: ' . $grnItem->grn->grn_number . ' - Rejected Material',
                    'status'        => 'pending',
                ]);

                PurchaseReturnItem::create([
                    'purchase_return_id' => $return->id,
                    'product_id'         => $grnItem->product_id,
                    'qty'                => $grnItem->rejected_qty,
                    'price'              => $grnItem->unit_price,
                    'line_total'         => round($grnItem->rejected_qty * $grnItem->unit_price, 2),
                ]);

                $grnItem->update(['debit_note_created' => true, 'debit_note_id' => $return->id]);

                // Update PO status
                $purchase->load('items', 'grns.items', 'returns');
                $purchase->update(['po_status' => $purchase->recomputePoStatus()]);
            });

            return back()->with('success', 'Debit note created automatically.');
        }

        // Manual mode - redirect to purchase return form
        return redirect()->route('purchase.return.show', $purchase->id)
            ->with('info', 'Please create the debit note manually for rejected items.');
    }

    /**
     * Internal: update warehouse_stocks
     */
    private function updateWarehouseStock(int $productId, int $warehouseId, float $qtyDelta): void
    {
        $product = Product::find($productId);
        $ppb     = ($product && $product->pieces_per_box > 0) ? $product->pieces_per_box : 1;

        $stock = WarehouseStock::where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->lockForUpdate()
            ->first();

        if ($stock) {
            $stock->total_pieces = max(0, $stock->total_pieces + $qtyDelta);
            $stock->quantity     = $stock->total_pieces / $ppb;
            $stock->save();
        } else {
            WarehouseStock::create([
                'warehouse_id' => $warehouseId,
                'product_id'   => $productId,
                'total_pieces' => max(0, $qtyDelta),
                'quantity'     => max(0, $qtyDelta) / $ppb,
                'price'        => 0,
            ]);
        }
    }

    public function destroy($id)
    {
        $grn = GrnReceipt::findOrFail($id);
        if ($grn->status === 'confirmed') {
            return back()->with('error', 'Cannot delete a confirmed GRN. Please create a return instead.');
        }
        $grn->delete();
        return redirect()->route('grn.index')->with('success', 'GRN deleted.');
    }
}

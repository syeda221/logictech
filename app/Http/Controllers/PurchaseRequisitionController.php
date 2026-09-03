<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionItem;
use App\Models\Vendor;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseRequisitionController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseRequisition::with(['branch', 'warehouse', 'requester', 'approver', 'items.product']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        if ($request->filled('search')) {
            $query->where('pr_number', 'like', '%' . $request->search . '%');
        }

        $requisitions = $query->latest()->paginate(20);

        $stats = [
            'total'    => PurchaseRequisition::count(),
            'pending'  => PurchaseRequisition::where('status', 'pending_approval')->count(),
            'approved' => PurchaseRequisition::where('status', 'approved')->count(),
            'closed'   => PurchaseRequisition::where('status', 'closed')->count(),
        ];

        return view('admin_panel.purchase_requisition.index', compact('requisitions', 'stats'));
    }

    public function create()
    {
        $branches = Branch::all();
        if ($branches->isEmpty()) {
            $defaultBranch = Branch::create([
                'name'    => 'Main Head Office',
                'address' => 'Industrial Area',
                'number'  => '0000-0000000',
                'user_id' => Auth::id() ?? 1,
            ]);
            $branches = collect([$defaultBranch]);
        }
        $warehouses = Warehouse::all();
        $products   = Product::where('is_active', 1)
            ->orderBy('item_name')
            ->get(['id', 'item_name', 'item_code', 'size_mode', 'purchase_price_per_piece']);
        return view('admin_panel.purchase_requisition.create', compact('branches', 'warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id'      => 'nullable|exists:branches,id',
            'warehouse_id'   => 'required|exists:warehouses,id',
            'required_by_date' => 'nullable|date',
            'notes'          => 'nullable|string',
            'items'          => 'required|array|min:1',
            'items.*.product_id'   => 'required|exists:products,id',
            'items.*.required_qty' => 'required|numeric|min:0.001',
        ]);

        $branchId = $request->branch_id;
        if (!$branchId) {
            $branch = Branch::first();
            if (!$branch) {
                $branch = Branch::create([
                    'name'    => 'Main Head Office',
                    'address' => 'Industrial Area',
                    'number'  => '0000-0000000',
                    'user_id' => Auth::id() ?? 1,
                ]);
            }
            $branchId = $branch->id;
        }

        DB::transaction(function () use ($request, $branchId) {
            $pr = PurchaseRequisition::create([
                'pr_number'        => PurchaseRequisition::generatePrNumber(),
                'branch_id'        => $branchId,
                'warehouse_id'     => $request->warehouse_id,
                'requested_by'     => Auth::id(),
                'status'           => 'pending_approval',
                'required_by_date' => $request->required_by_date,
                'notes'            => $request->notes,
            ]);

            foreach ($request->items as $item) {
                PurchaseRequisitionItem::create([
                    'pr_id'                => $pr->id,
                    'product_id'           => $item['product_id'],
                    'required_qty'         => $item['required_qty'],
                    'uom'                  => $item['uom'] ?? null,
                    'estimated_unit_price' => $item['estimated_unit_price'] ?? $item['estimated_price'] ?? 0,
                    'reason'               => $item['reason'] ?? null,
                ]);
            }
        });

        return redirect()->route('purchase-requisitions.index')
            ->with('success', 'Purchase Requisition created successfully and sent for approval.');
    }

    public function show($id)
    {
        $pr = PurchaseRequisition::with(['branch', 'warehouse', 'requester', 'approver', 'items.product', 'purchaseOrders'])
            ->findOrFail($id);
        return view('admin_panel.purchase_requisition.show', compact('pr'));
    }

    public function approve(Request $request, $id)
    {
        $pr = PurchaseRequisition::findOrFail($id);

        if ($pr->status !== 'pending_approval') {
            return back()->with('error', 'This PR is not in pending approval status.');
        }

        $pr->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Purchase Requisition approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['rejection_reason' => 'required|string']);

        $pr = PurchaseRequisition::findOrFail($id);
        $pr->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Purchase Requisition rejected.');
    }

    public function convertToPO($id)
    {
        $pr = PurchaseRequisition::with(['items.product', 'branch', 'warehouse'])
            ->findOrFail($id);

        if ($pr->status !== 'approved') {
            return back()->with('error', 'Only approved requisitions can be converted to PO.');
        }

        $vendors    = Vendor::where('is_active', 1)->orderBy('name')->get();
        $warehouses = Warehouse::all();
        $branches   = Branch::all();

        return view('admin_panel.purchase_requisition.convert_to_po', compact('pr', 'vendors', 'warehouses', 'branches'));
    }

    public function destroy($id)
    {
        $pr = PurchaseRequisition::findOrFail($id);
        if (in_array($pr->status, ['approved', 'partially_ordered', 'closed'])) {
            return back()->with('error', 'Cannot delete an approved or closed PR.');
        }
        $pr->delete();
        return redirect()->route('purchase-requisitions.index')
            ->with('success', 'Purchase Requisition deleted.');
    }
}

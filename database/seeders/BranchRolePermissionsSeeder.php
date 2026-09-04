<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class BranchRolePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds for Branch & Branch Manager roles.
     *
     * Usage: php artisan db:seed --class=BranchRolePermissionsSeeder
     */
    public function run(): void
    {
        // 1. Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 2. Create or find roles
        $branchManagerRole = Role::firstOrCreate(['name' => 'Branch Manager', 'guard_name' => 'web']);
        $branchStaffRole   = Role::firstOrCreate(['name' => 'Branch', 'guard_name' => 'web']);

        $allPermissions = Permission::all();

        // ── Branch Manager Permissions (Full Management & Approvals) ──
        $managerPatterns = [
            // Core
            'home.view', 'profile.*',

            // Products & Catalog
            'products.*', 'categories.*', 'subcategories.*', 'brands.*', 'units.*',
            'discount.products.*', 'product.bookings.*', 'web_products.*',

            // Inventory & Warehouse
            'stocks.*', 'stock.adjust.*', 'stock.transfer.*', 'inventory.onhand.*',
            'item.stock.report.*', 'warehouse.*', 'warehouse.stock.*',

            // Purchase & Supply Chain
            'purchases.*', 'purchase.returns.*', 'purchase.report.*',
            'inward.gatepass.*', 'vendors.*', 'vendor.ledger.*', 'vendor.bilties.*', 'payable.report.*',

            // Sales, POS & Customers
            'sales.*', 'sales.officers.*', 'sales.returns.*', 'sale.report.*',
            'customers.*', 'customer.ledger.*', 'coupons.*',

            // Accounts & Vouchers
            'expense.voucher.*', 'income.voucher.*', 'receipts.voucher.*', 'payment.voucher.*',
            'parties.balance.report.*', 'aging.report.*', 'reporting.*'
        ];

        $managerPerms = $this->matchPermissions($allPermissions, $managerPatterns);
        $branchManagerRole->syncPermissions($managerPerms);
        $this->command->info("✓ Role 'Branch Manager' configured with " . count($managerPerms) . " permissions.");

        // ── Branch Staff / Operator Permissions (Operational Day-to-Day) ──
        $staffPatterns = [
            // Core
            'home.view', 'profile.view', 'profile.edit',

            // Products & Catalog (View & Create)
            'products.view', 'products.create', 'products.edit',
            'categories.view', 'subcategories.view', 'brands.view', 'units.view', 'discount.products.view',

            // Inventory & Transfers
            'stocks.view', 'stock.transfer.view', 'stock.transfer.create', 'stock.transfer.edit',
            'inventory.onhand.view', 'item.stock.report.view', 'warehouse.stock.view', 'warehouse.view',

            // Purchases (Requisitions & Gatepass Creation)
            'purchases.view', 'purchases.create', 'inward.gatepass.view', 'inward.gatepass.create',
            'vendors.view', 'vendor.ledger.view', 'vendor.bilties.view',

            // Sales, POS & Customers
            'sales.view', 'sales.create', 'sales.edit',
            'sales.returns.view', 'sales.returns.create',
            'customers.view', 'customers.create', 'customers.edit', 'customer.ledger.view',

            // Basic Vouchers & Receipts
            'receipts.voucher.view', 'receipts.voucher.create',
            'expense.voucher.view', 'expense.voucher.create',
            'sale.report.view'
        ];

        $staffPerms = $this->matchPermissions($allPermissions, $staffPatterns);
        $branchStaffRole->syncPermissions($staffPerms);
        $this->command->info("✓ Role 'Branch' configured with " . count($staffPerms) . " permissions.");

        // 3. Reset cache again
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->command->info("✓ Permissions cache cleared successfully.");
    }

    /**
     * Helper to match permission names against wildcard patterns.
     */
    private function matchPermissions($allPermissions, array $patterns): array
    {
        $matched = [];
        foreach ($allPermissions as $perm) {
            foreach ($patterns as $pattern) {
                if ($pattern === $perm->name) {
                    $matched[$perm->name] = $perm;
                    break;
                }
                $regex = '/^' . str_replace(['.', '*'], ['\.', '.*'], $pattern) . '$/';
                if (preg_match($regex, $perm->name)) {
                    $matched[$perm->name] = $perm;
                    break;
                }
            }
        }
        return array_values($matched);
    }
}

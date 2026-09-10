<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class BranchManagerSeeder extends Seeder
{
    /**
     * Run the database seeds for Branch Manager.
     * Grants ONLY:
     * 1. Vendors
     * 2. Purchase Orders (PO)
     * 3. Material Usage & Reporting
     * 4. Sales
     * 5. Customers
     *
     * All other permissions (warehouse, stock transfer/adjust, POS system, PR, GRN,
     * vouchers, products, HR, etc.) are strictly hidden/revoked.
     *
     * Usage: php artisan db:seed --class=BranchManagerSeeder
     */
    public function run(): void
    {
        // 1. Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 2. Ensure all relevant permissions exist in the database
        $requiredPerms = [
            'home.view',
            'pos.view',
            'purchase.requisitions.view',
            'purchase.requisitions.create',
            'purchase.requisitions.edit',
            'purchase.requisitions.delete',
            'grn.view',
            'grn.create',
            'grn.edit',
            'grn.delete',
            'material.usage.view',
            'material.usage.create',
            'material.usage.edit',
            'material.usage.delete',
            'material.usage.report.view',
        ];

        foreach ($requiredPerms as $permName) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
        }

        // 3. Strict Allowed Patterns for Branch Manager ONLY:
        // (Vendor, Purchase Order, Material Usage, Sale, Customer)
        $allowedPatterns = [
            // Basic Dashboard & Profile
            'home.view',
            'profile.*',

            // 1. Vendors / Suppliers
            'vendors.*',
            'vendor.ledger.*',

            // 2. Purchase Orders (PO)
            'purchases.*',
            'purchase.report.*',

            // 3. Material Usage & Reporting
            'material.usage.*',
            'material.usage.report.*',

            // 4. Sales (excluding sales officers)
            'sales.view',
            'sales.create',
            'sales.edit',
            'sales.delete',
            'sales.returns.*',
            'sale.report.*',

            // 5. Customers
            'customers.*',
            'customer.ledger.*',
        ];

        $allPermissions = Permission::all();
        $managerPerms = $this->matchPermissions($allPermissions, $allowedPatterns);

        // 4. Find or create the Branch Manager role & strictly sync permissions
        $branchManagerRole = Role::firstOrCreate(['name' => 'Branch Manager', 'guard_name' => 'web']);
        $branchManagerRole->syncPermissions($managerPerms);

        // 5. Ensure Super Admin role retains all permissions
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions(Permission::all());
        }

        // 6. Reset cached permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command->info("✓ Branch Manager configured strictly with only Vendors, Purchase Orders, Material Usage, Sales, and Customers (" . count($managerPerms) . " permissions).");
        $this->command->info("✓ All other menus/modules hidden from Branch Manager.");
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

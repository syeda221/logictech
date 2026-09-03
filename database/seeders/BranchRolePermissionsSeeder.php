<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class BranchRolePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 2. Define Branch roles
        $roles = [
            Role::firstOrCreate(['name' => 'Branch', 'guard_name' => 'web']),
            Role::firstOrCreate(['name' => 'Branch Manager', 'guard_name' => 'web']),
        ];

        // 3. Permission patterns for Product & Purchase modules
        $permissionPatterns = [
            // Product & Catalog
            'products.*',
            'categories.*',
            'subcategories.*',
            'brands.*',
            'units.*',
            'discount.products.*',
            'product.bookings.*',
            'web_products.*',

            // Stock & Inventory
            'stocks.*',
            'stock.adjust.*',
            'stock.transfer.*',
            'inventory.onhand.*',
            'item.stock.report.*',
            'warehouse.stock.*',
            'warehouse.*',

            // Purchase & Supply Chain
            'purchases.*',
            'purchase.returns.*',
            'purchase.report.*',
            'inward.gatepass.*',
            'vendors.*',
            'vendor.ledger.*',
            'vendor.bilties.*',
            'payable.report.*',

            // Base access
            'home.view',
            'profile.view',
            'profile.edit',
        ];

        $allPermissions = Permission::all();
        $matchedPermissions = [];

        foreach ($allPermissions as $perm) {
            foreach ($permissionPatterns as $pattern) {
                if ($pattern === $perm->name) {
                    $matchedPermissions[$perm->name] = $perm;
                    break;
                }
                $regex = '/^' . str_replace(['.', '*'], ['\.', '.*'], $pattern) . '$/';
                if (preg_match($regex, $perm->name)) {
                    $matchedPermissions[$perm->name] = $perm;
                    break;
                }
            }
        }

        $matchedList = array_values($matchedPermissions);

        foreach ($roles as $role) {
            $role->syncPermissions($matchedList);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}

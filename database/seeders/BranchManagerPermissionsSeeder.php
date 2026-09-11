<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class BranchManagerPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds the exact permissions assigned to the Branch Manager role.
     *
     * Usage: php artisan db:seed --class=BranchManagerPermissionsSeeder
     */
    public function run(): void
    {
        // 1. Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 2. Ensure Branch Manager role exists
        $role = Role::firstOrCreate(['name' => 'Branch Manager', 'guard_name' => 'web']);

        // 3. Exact permissions assigned to Branch Manager
        $permissions = [
            'brands.create',
            'brands.delete',
            'brands.edit',
            'brands.view',
            'categories.create',
            'categories.delete',
            'categories.edit',
            'categories.view',
            'chart.of.accounts.create',
            'chart.of.accounts.delete',
            'chart.of.accounts.edit',
            'chart.of.accounts.view',
            'customer.ledger.create',
            'customer.ledger.delete',
            'customer.ledger.edit',
            'customer.ledger.view',
            'customers.create',
            'customers.delete',
            'customers.edit',
            'customers.view',
            'expense.voucher.create',
            'expense.voucher.delete',
            'expense.voucher.edit',
            'expense.voucher.view',
            'home.view',
            'item.stock.report.create',
            'item.stock.report.delete',
            'item.stock.report.edit',
            'item.stock.report.view',
            'journal.voucher.create',
            'journal.voucher.delete',
            'journal.voucher.edit',
            'journal.voucher.view',
            'material.usage.create',
            'material.usage.delete',
            'material.usage.edit',
            'material.usage.report.view',
            'material.usage.view',
            'narrations.create',
            'narrations.delete',
            'narrations.edit',
            'narrations.view',
            'parties.balance.report.create',
            'parties.balance.report.delete',
            'parties.balance.report.edit',
            'parties.balance.report.view',
            'payment.voucher.create',
            'payment.voucher.delete',
            'payment.voucher.edit',
            'payment.voucher.view',
            'products.create',
            'products.delete',
            'products.edit',
            'products.view',
            'profile.create',
            'profile.delete',
            'profile.edit',
            'profile.view',
            'purchase.report.create',
            'purchase.report.delete',
            'purchase.report.edit',
            'purchase.report.view',
            'purchase.returns.create',
            'purchase.returns.delete',
            'purchase.returns.edit',
            'purchase.returns.view',
            'purchases.create',
            'purchases.delete',
            'purchases.edit',
            'purchases.view',
            'receipts.voucher.create',
            'receipts.voucher.delete',
            'receipts.voucher.edit',
            'receipts.voucher.view',
            'reporting.create',
            'reporting.delete',
            'reporting.edit',
            'reporting.view',
            'sale.report.create',
            'sale.report.delete',
            'sale.report.edit',
            'sale.report.view',
            'sales.create',
            'sales.delete',
            'sales.edit',
            'sales.returns.create',
            'sales.returns.delete',
            'sales.returns.edit',
            'sales.returns.view',
            'sales.view',
            'settings.add',
            'settings.create',
            'settings.delete',
            'settings.edit',
            'settings.read',
            'settings.update',
            'settings.view',
            'subcategories.create',
            'subcategories.delete',
            'subcategories.edit',
            'subcategories.view',
            'units.create',
            'units.delete',
            'units.edit',
            'units.view',
            'vendor.ledger.create',
            'vendor.ledger.delete',
            'vendor.ledger.edit',
            'vendor.ledger.view',
            'vendors.create',
            'vendors.delete',
            'vendors.edit',
            'vendors.view',
            'zones.create',
            'zones.delete',
            'zones.edit',
            'zones.view',
        ];

        // 4. Ensure each permission exists in database before syncing
        foreach ($permissions as $permName) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
        }

        // 5. Sync permissions with Branch Manager role
        $role->syncPermissions($permissions);

        // 6. Reset cache again
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        if (isset($this->command)) {
            $this->command->info("✓ Role 'Branch Manager' successfully synced with " . count($permissions) . " permissions.");
        }
    }
}
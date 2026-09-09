<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class BranchManagerSeeder extends Seeder
{
    /**
     * Run the database seeds to assign Material Usage & Material Usage Reporting permissions
     * to the Branch Manager role.
     *
     * Usage: php artisan db:seed --class=BranchManagerSeeder
     */
    public function run(): void
    {
        // 1. Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 2. Ensure Material Usage & Material Usage Reporting permissions exist
        $permissions = [
            'material.usage.view',
            'material.usage.create',
            'material.usage.edit',
            'material.usage.delete',
            'material.usage.report.view',
        ];

        foreach ($permissions as $permName) {
            Permission::firstOrCreate([
                'name' => $permName,
                'guard_name' => 'web',
            ]);
        }

        // 3. Find or create Branch Manager role
        $branchManagerRole = Role::firstOrCreate([
            'name' => 'Branch Manager',
            'guard_name' => 'web',
        ]);

        // 4. Assign permissions to Branch Manager role
        $branchManagerRole->givePermissionTo($permissions);

        // 5. Ensure Super Admin also has these permissions
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }

        // 6. Clear permissions cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command->info("✓ Successfully assigned Material Usage and Material Usage Reporting permissions to 'Branch Manager' role.");
        foreach ($permissions as $perm) {
            $this->command->line("  - {$perm}");
        }
    }
}

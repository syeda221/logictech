<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Branch;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class BranchManagerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates user admin@admin.com with password 808080 and assigns Branch Manager role.
     *
     * Usage: php artisan db:seed --class=BranchManagerUserSeeder
     */
    public function run(): void
    {
        // 1. Ensure Branch Manager role exists
        $role = Role::firstOrCreate(['name' => 'Branch Manager', 'guard_name' => 'web']);

        // 2. Create or Update user admin@admin.com
        $user = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Branch Manager Admin',
                'password' => Hash::make('808080'),
            ]
        );

        // 4. Assign Branch Manager role
        $user->syncRoles([$role->name]);

        if (isset($this->command)) {
            $this->command->info("✓ User 'admin@admin.com' created/updated with password '808080' and assigned role 'Branch Manager'.");
        }
    }
}
<?php

// File: database/seeders/RoleSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Daftar role beserta rbac_tier-nya
        $roles = [
            // Tier 1 (Edit - Full Access)
            ['name' => 'CSP', 'rbac_tier' => 1, 'description' => 'Creative Strategy Planner'],
            ['name' => 'SMS', 'rbac_tier' => 1, 'description' => 'Social Media Specialist'],

            // Tier 2 (Comment - Review Access)
            ['name' => 'CW', 'rbac_tier' => 2, 'description' => 'Copywriter'],
            ['name' => 'GVD', 'rbac_tier' => 2, 'description' => 'Graphic Visual Designer'],
            ['name' => 'CC', 'rbac_tier' => 2, 'description' => 'Creative Consultant'],
            ['name' => 'VG', 'rbac_tier' => 2, 'description' => 'Video Graphic'],
            ['name' => 'ASM', 'rbac_tier' => 2, 'description' => 'Area Sales Manager'],
            ['name' => 'RnD', 'rbac_tier' => 2, 'description' => 'Research and Development'],
            ['name' => 'Legal', 'rbac_tier' => 2, 'description' => 'Legal'],

            // Tier 3 (View - Read Only)
            ['name' => 'MC_BM', 'rbac_tier' => 3, 'description' => 'Marketing Campaign Brand Manager'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                [
                    'guard_name' => 'web',
                    'rbac_tier' => $role['rbac_tier'],
                    'description' => $role['description'] ?? null,
                ]
            );
        }
    }
}

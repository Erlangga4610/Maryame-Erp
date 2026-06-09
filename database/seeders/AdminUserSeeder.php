<?php

// File: database/seeders/AdminUserSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat user admin pertama (CSP)
        $admin = User::updateOrCreate(
            ['email' => 'admin@maryame.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password123'),
                'employee_id' => 'EMP-001',
                'position' => 'Creative Strategy Planner',
                'department' => 'Creative',
                'is_active' => true,
            ]
        );

        // Assign role CSP
        $admin->assignRole('CSP');

        // Buat sample user untuk testing (opsional)
        $sampleRoles = ['SMS', 'CW', 'GVD', 'CC', 'VG', 'ASM', 'RnD', 'Legal', 'MC_BM'];

        foreach ($sampleRoles as $index => $roleName) {
            User::updateOrCreate(
                ['email' => strtolower($roleName).'@maryame.com'],
                [
                    'name' => $roleName.' User',
                    'password' => bcrypt('password123'),
                    'employee_id' => 'EMP-'.str_pad($index + 2, 3, '0', STR_PAD_LEFT),
                    'position' => $roleName,
                    'department' => $this->getDepartment($roleName),
                    'is_active' => true,
                ]
            )->assignRole($roleName);
        }
    }

    private function getDepartment(string $role): string
    {
        return match ($role) {
            'CSP', 'CW', 'GVD', 'CC', 'VG' => 'Creative',
            'SMS', 'MC_BM' => 'Marketing',
            'ASM' => 'Sales',
            'RnD' => 'Research',
            'Legal' => 'Legal',
            default => 'General',
        };
    }
}

<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'Super Admin'],
            ['rbac_tier' => 0, 'guard_name' => 'web'],
        );

        $admin = User::where('email', 'admin@maryame.com')->first();
        if ($admin && ! $admin->hasRole('Super Admin')) {
            $admin->assignRole('Super Admin');
        }
    }

    public function down(): void
    {
        $role = Role::where('name', 'Super Admin')->first();
        if ($role) {
            $role->delete();
        }
    }
};

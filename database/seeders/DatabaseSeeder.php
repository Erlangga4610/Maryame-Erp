<?php

// File: database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // First: Roles & Users
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
        ]);

        // Second: Master Data
        $this->call([
            PlatformSeeder::class,
            ProductSeeder::class,
            CampaignSeeder::class,
        ]);
    }
}

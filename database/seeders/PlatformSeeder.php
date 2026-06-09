<?php

// File: database/seeders/PlatformSeeder.php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        $platforms = [
            // Social Media Platforms
            ['name' => 'TikTok', 'code' => 'TKM', 'originality_strict' => true],
            ['name' => 'Instagram Feed', 'code' => 'IGF', 'originality_strict' => true],
            ['name' => 'Instagram Reels', 'code' => 'IGR', 'originality_strict' => true],
            ['name' => 'Instagram Stories', 'code' => 'IGS', 'originality_strict' => false],
            ['name' => 'Facebook', 'code' => 'FB', 'originality_strict' => false],

            // E-commerce Platforms
            ['name' => 'Shopee', 'code' => 'SHP', 'originality_strict' => false],
            ['name' => 'Lazada', 'code' => 'LZD', 'originality_strict' => false],
            ['name' => 'Tokopedia', 'code' => 'TWP', 'originality_strict' => false],

            // Other Channels
            ['name' => 'Website', 'code' => 'WEB', 'originality_strict' => false],
            ['name' => 'Blog', 'code' => 'BLG', 'originality_strict' => false],
            ['name' => 'WhatsApp', 'code' => 'WA', 'originality_strict' => false],
            ['name' => 'X (Twitter)', 'code' => 'X', 'originality_strict' => false],
        ];

        foreach ($platforms as $platform) {
            Platform::updateOrCreate(
                ['code' => $platform['code']],
                [
                    'name' => $platform['name'],
                    'originality_strict' => $platform['originality_strict'],
                    'is_active' => true,
                ]
            );
        }
    }
}

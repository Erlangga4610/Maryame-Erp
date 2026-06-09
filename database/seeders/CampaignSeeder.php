<?php

// File: database/seeders/CampaignSeeder.php

namespace Database\Seeders;

use App\Models\Campaign;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $campaigns = [
            [
                'name' => 'Glowing Ramadan 2026',
                'type' => 'seasonal',
                'start_date' => $now->copy()->startOfMonth(),
                'end_date' => $now->copy()->endOfMonth()->addDays(7),
                'objective' => 'Meningkatkan brand awareness selama Ramadan dengan fokus pada produk brightening',
            ],
            [
                'name' => 'New Serum Launch',
                'type' => 'launching',
                'start_date' => $now->copy()->addDays(30),
                'end_date' => $now->copy()->addDays(60),
                'objective' => 'Launch product baru: Maryamé Niacinamide Serum',
            ],
            [
                'name' => '12.12 Sale Blast',
                'type' => 'tactical',
                'start_date' => $now->copy()->addMonths(6),
                'end_date' => $now->copy()->addMonths(6)->addDays(7),
                'objective' => 'Mendorong sales melalui campaign diskon 12.12',
            ],
            [
                'name' => 'Summer Glow 2026',
                'type' => 'seasonal',
                'start_date' => $now->copy()->addMonths(3),
                'end_date' => $now->copy()->addMonths(4),
                'objective' => 'Campaign musim panas dengan fokus sunscreen dan hydrating products',
            ],
            [
                'name' => 'Clear Skin Journey',
                'type' => 'tactical',
                'start_date' => $now->copy()->addDays(15),
                'end_date' => $now->copy()->addDays(45),
                'objective' => 'Edukasi skincare untuk acne-prone skin',
            ],
        ];

        foreach ($campaigns as $campaign) {
            Campaign::updateOrCreate(
                [
                    'name' => $campaign['name'],
                    'start_date' => $campaign['start_date'],
                ],
                $campaign
            );
        }
    }
}

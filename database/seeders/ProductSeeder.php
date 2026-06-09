<?php

// File: database/seeders/ProductSeeder.php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Skincare Products
            ['sku' => 'MYM-SERUM-001', 'name' => 'Maryamé Brightening Serum', 'category' => 'Serum'],
            ['sku' => 'MYM-SERUM-002', 'name' => 'Maryamé Retinol Serum', 'category' => 'Serum'],
            ['sku' => 'MYM-MOIST-001', 'name' => 'Maryamé Hydrating Moisturizer', 'category' => 'Moisturizer'],
            ['sku' => 'MYM-MOIST-002', 'name' => 'Maryamé Matte Moisturizer', 'category' => 'Moisturizer'],
            ['sku' => 'MYM-CLEAN-001', 'name' => 'Maryamé Gentle Cleanser', 'category' => 'Cleanser'],
            ['sku' => 'MYM-CLEAN-002', 'name' => 'Maryamé Salicylic Cleanser', 'category' => 'Cleanser'],
            ['sku' => 'MYM-SUN-001', 'name' => 'Maryamé Sunscreen SPF 50', 'category' => 'Sunscreen'],
            ['sku' => 'MYM-TONER-001', 'name' => 'Maryamé Hydrating Toner', 'category' => 'Toner'],
            ['sku' => 'MYM-EYE-001', 'name' => 'Maryamé Eye Cream', 'category' => 'Eye Care'],
            ['sku' => 'MYM-MASK-001', 'name' => 'Maryamé Clay Mask', 'category' => 'Mask'],

            // Bundle Products
            ['sku' => 'MYM-BUNDLE-001', 'name' => 'Maryamé Starter Kit', 'category' => 'Bundle'],
            ['sku' => 'MYM-BUNDLE-002', 'name' => 'Maryamé Brightening Set', 'category' => 'Bundle'],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['sku' => $product['sku']],
                [
                    'name' => $product['name'],
                    'category' => $product['category'],
                    'description' => "Product {$product['name']} from Maryamé",
                    'is_active' => true,
                ]
            );
        }
    }
}

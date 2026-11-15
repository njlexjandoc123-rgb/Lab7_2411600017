<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'sku' => 'UNI-IT-001',
                'name' => 'IT Department Uniform (Medium)',
                'brand' => 'School Apparel',
                'category' => 'Uniforms',
                'qty' => 45,
                'reorder_level' => 10,
                'unit_price' => 450.00,
            ],
            [
                'sku' => 'BK-WST-101',
                'name' => 'Web Systems & Technologies Textbook',
                'brand' => 'C&E Publishing',
                'category' => 'Textbooks',
                'qty' => 30,
                'reorder_level' => 8,
                'unit_price' => 680.00,
            ],
            [
                'sku' => 'ACC-LAN-005',
                'name' => 'Student ID Lanyard Set',
                'brand' => 'Campus Merch',
                'category' => 'Accessories',
                'qty' => 120,
                'reorder_level' => 20,
                'unit_price' => 75.00,
            ],
            [
                'sku' => 'PE-TSH-002',
                'name' => 'PE T-Shirt (Large)',
                'brand' => 'Sportswear Co.',
                'category' => 'Uniforms',
                'qty' => 3,
                'reorder_level' => 5,
                'unit_price' => 250.00,
            ],
            [
                'sku' => 'SCI-MAN-2026',
                'name' => 'Science Lab Manual 2026',
                'brand' => 'University Press',
                'category' => 'Textbooks',
                'qty' => 2,
                'reorder_level' => 5,
                'unit_price' => 320.00,
            ],
        ];

        foreach ($products as $product) {
            Brand::firstOrCreate(['name' => $product['brand']]);
            Category::firstOrCreate(['name' => $product['category']]);
            Product::firstOrCreate(['sku' => $product['sku']], $product);
        }
    }
}

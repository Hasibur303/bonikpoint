<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class AtvsGenxProductSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::where('slug', 'pod-devices')->first()
            ?? Category::where('slug', 'rechargeable-pod-system')->first()
            ?? Category::where('slug', 'vape-accessories')->first();

        if (! $category) {
            return;
        }

        Product::updateOrCreate(
            ['slug' => 'atvs-genx-rechargeable-pod-device'],
            [
                'category_id' => $category->id,
                'name' => 'ATVS GENX Rechargeable Pod Device',
                'description' => <<<'TEXT'
The ATVS GENX Rechargeable Pod Device is a compact and stylish vaping device engineered for convenience, smooth performance, and portability. Designed with a premium ergonomic body and advanced airflow, it delivers a satisfying vaping experience while remaining lightweight enough for everyday carry.

Perfect for users seeking a sleek, easy-to-use pod system with reliable battery life and consistent flavor production.

Key Features
* Premium Compact Design
* Ergonomic Anti-Slip Body
* Rechargeable via USB Type-C
* 500mAh Built-in Battery
* 2ml Refillable Pod Capacity
* Smooth & Rich Flavor
* Leak-Resistant Design
* Draw-Activated Firing
* Lightweight & Pocket Friendly
* Easy Pod Replacement
* Durable Construction
* Beginner Friendly

Technical Specifications
Brand: ATVS
Model: GENX
Product Type: Rechargeable Pod Device
Battery Capacity: 500mAh Built-in Battery
Pod Capacity: 2ml
Resistance: 0.6Ω / 0.8Ω
Device Size: 19 x 19 x 106 mm
Activation: Draw Activated
Charging Port: USB Type-C
Material: Premium PC + PCTG
Color: Black
Origin: Made in China

Package Includes
* 1 x ATVS GENX Device
* 1 x ATVS GENX Pod
* 1 x User Manual (if included)
* 1 x Original Retail Box

Safety Information
* For Adults (18+) Only.
* This product contains nicotine when used with nicotine e-liquid.
* Nicotine is a highly addictive substance.
* Keep out of reach of children and pets.
* Store in a cool and dry place.
* Do not expose to direct sunlight or high temperatures.
* Not recommended for pregnant or breastfeeding women.

Why Choose ATVS GENX?
* Long-lasting 500mAh rechargeable battery
* Smooth and consistent vapor production
* Compact, modern, and stylish design
* Comfortable grip for everyday use
* Reliable performance with 0.6Ω / 0.8Ω pod options
* Ideal for both new and experienced vapers

Note: এই মডেলটি Disposable Vape নয়, এটি একটি Rechargeable Pod Device। তাই ক্যাটাগরি হিসেবে Pod Device বা Rechargeable Pod System ব্যবহার করাই সঠিক।
TEXT,
                'price' => 1890,
                'compare_price' => 2000,
                'stock' => 10,
                'sku' => 'ATVS-GENX-POD-BLK',
                'image' => 'assets/images/product/atvs-genx-rechargeable-pod-device.jpg',
                'is_featured' => true,
                'is_active' => true,
            ]
        );
    }
}

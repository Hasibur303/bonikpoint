<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class SmokVapePen22ProductSeeder extends Seeder
{
    public function run(): void
    {
        $parent = Category::where('slug', 'vape-accessories')->first();

        if (! $parent) {
            return;
        }

        $category = Category::updateOrCreate(
            ['slug' => 'vape-starter-kits'],
            [
                'name' => 'Vape Starter Kits',
                'parent_id' => $parent->id,
                'description' => 'Complete vape kits and pen-style starter devices for daily use.',
                'is_active' => true,
            ]
        );

        Product::updateOrCreate(
            ['slug' => 'smok-vape-pen-22'],
            [
                'category_id' => $category->id,
                'name' => 'SMOK Vape Pen 22',
                'description' => <<<'TEXT'
The SMOK Vape Pen 22 is a powerful yet easy-to-use all-in-one vape starter kit designed for both beginners and experienced vapers. Featuring a 1650mAh built-in battery, 2ml tank capacity, and a 22mm compact design, it delivers excellent flavor, smooth vapor production, and long-lasting performance. The simple one-button operation and top-fill system make refilling and daily use quick and convenient, while the optimized airflow provides a satisfying MTL and restricted DTL vaping experience.

Key Features
* All-in-One Vape Pen Design
* Built-in 1650mAh Battery
* 22mm Stainless Steel Construction
* 2ml E-Liquid Capacity
* One-Button Operation
* Top-Fill Tank Design
* Optimized Airflow System
* Rich Flavor & Dense Vapor
* Fast USB Charging
* LED Battery Indicator
* Leak-Resistant Design
* Compact & Lightweight
* Beginner Friendly
* Durable Build Quality

Specifications
Brand: SMOK
Model: Vape Pen 22
Device Type: All-in-One Vape Kit
Battery Capacity: 1650mAh Built-in
Tank Capacity: 2ml
Tank Diameter: 22mm
Coil Resistance: 0.3 ohm Dual Core Coil
Charging: Micro USB
Filling System: Top Fill
Airflow: Bottom Adjustable Airflow
Material: Stainless Steel + Pyrex Glass
Activation: One-Button Firing
Color: Black
Vaping Style: MTL / Restricted DTL

Package Includes
* 1 x SMOK Vape Pen 22 Device
* 1 x Vape Pen 22 Tank (2ml)
* 1 x 0.3 ohm Dual Core Coil (Pre-installed)
* 1 x Replacement 0.3 ohm Coil
* 1 x Micro USB Charging Cable
* 1 x User Manual
* 1 x Original Retail Box

Safety Information
* For Adults (18+) Only.
* Keep out of reach of children and pets.
* Do not expose the device to water or extreme heat.
* Use only compatible coils and e-liquids.
* Charge using a quality USB power source.
* This device is intended for adult users only.
* If used with nicotine e-liquid, nicotine is a highly addictive substance.

Why Choose SMOK Vape Pen 22?
* Powerful 1650mAh built-in battery
* Compact and portable pen-style design
* Smooth flavor and dense vapor production
* Easy top-fill refilling system
* Durable stainless steel construction
* Perfect for both beginners and experienced vapers
* Reliable performance for everyday vaping
TEXT,
                'price' => 1650,
                'compare_price' => 2100,
                'stock' => 10,
                'sku' => 'SMOK-VP22-BLK',
                'image' => 'assets/images/product/smok-vape-pen-22.jpg',
                'is_featured' => true,
                'is_active' => true,
            ]
        );
    }
}

<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class RincoeMantoNanoA4ProductSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::where('slug', 'pod-devices')->first()
            ?? Category::where('slug', 'vape-accessories')->first();

        if (! $category) {
            return;
        }

        Product::updateOrCreate(
            ['slug' => 'rincoe-manto-nano-a4'],
            [
                'category_id' => $category->id,
                'name' => 'Rincoe Manto Nano A4',
                'description' => <<<'TEXT'
The Rincoe Manto Nano A4 is a premium rechargeable pod system designed for both beginners and experienced vapers. Featuring a powerful 1000mAh built-in battery, adjustable airflow, and support for both MTL (Mouth-to-Lung) and RDL (Restricted Direct Lung) vaping styles, it delivers smooth flavor and reliable performance throughout the day.

Its compact, lightweight design makes it easy to carry, while the USB Type-C fast charging ensures minimal downtime. Compatible with Manto Nano Pod Cartridges, this device is ideal for users looking for convenience, portability, and consistent vapor production.

Key Features
* Powerful 1000mAh Built-in Battery
* Compatible with Manto Nano Pod Cartridges
* Adjustable Airflow Control
* Supports MTL & RDL Vaping
* USB Type-C Fast Charging
* Compact & Lightweight Design
* Ergonomic and Comfortable Grip
* LED Battery Indicator
* Premium Build Quality
* Easy Pod Installation
* Leak-Resistant Design
* Smooth & Consistent Flavor Delivery

Technical Specifications
* Brand: Rincoe
* Model: Manto Nano A4
* Product Type: Rechargeable Pod System
* Battery Capacity: 1000mAh
* Charging Port: USB Type-C
* Pod Compatibility: Manto Nano Cartridge
* Included Pod: 0.8 ohm / 2ml (MTL)
* Vaping Style: MTL / RDL
* Airflow: Adjustable
* Activation: Button & Draw Activated
* Color: Lime Blue
* Material: Zinc Alloy + PCTG
* Origin: Made in China

Package Includes
* 1 x Rincoe Manto Nano A4 Device (1000mAh)
* 1 x Manto Nano Cartridge (0.8 ohm / 2ml)
* 1 x Certificate Card
* 1 x Warranty Card
* 1 x User Manual

Safety Information
* For adults 21+ only.
* Keep away from children and pets.
* Do not expose the device to water or extreme temperatures.
* Not recommended for pregnant or breastfeeding women.
* If used with nicotine e-liquid, nicotine is a highly addictive substance.
TEXT,
                'price' => 1700,
                'compare_price' => 2200,
                'stock' => 10,
                'sku' => 'RIN-MANTO-A4-LB',
                'image' => 'assets/images/product/rincoe-manto-nano-a4.jpg',
                'is_featured' => true,
                'is_active' => true,
            ]
        );
    }
}

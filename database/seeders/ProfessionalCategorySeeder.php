<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProfessionalCategorySeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            [
                'name' => 'Vape & Accessories',
                'legacy' => ['Vape'],
                'children' => [
                    'Pod Devices',
                    'Disposable Vapes',
                    'Refill Pods',
                    'E-Liquids',
                    'Vape Coils',
                    'Vape Batteries',
                    'Chargers & Cables',
                    'Vape Parts & Accessories',
                ],
            ],
            [
                'name' => 'Winter Collection',
                'children' => [
                    'Winter Jackets',
                    'Hoodies & Sweatshirts',
                    'Sweaters & Cardigans',
                    'Thermal Wear',
                    'Winter Caps',
                    'Gloves',
                    'Scarves & Mufflers',
                    'Socks',
                    'Blankets & Comforters',
                ],
            ],
            [
                'name' => 'Kitchen Essentials',
                'children' => [
                    'Cookware',
                    'Kitchen Tools',
                    'Storage Containers',
                    'Dinnerware',
                    'Drinkware',
                    'Small Appliances',
                    'Cleaning Supplies',
                    'Baking Tools',
                    'Spice & Condiment Storage',
                ],
            ],
            [
                'name' => 'Home & Living',
                'children' => [
                    'Home Decor',
                    'Lighting',
                    'Bedding',
                    'Bathroom Essentials',
                    'Storage & Organization',
                    'Furniture',
                    'Cleaning Essentials',
                ],
            ],
            [
                'name' => 'Electronics & Gadgets',
                'children' => [
                    'Mobile Accessories',
                    'Smart Gadgets',
                    'Audio Devices',
                    'Chargers & Power Banks',
                    'Computer Accessories',
                ],
            ],
            [
                'name' => 'Beauty & Personal Care',
                'children' => [
                    'Skin Care',
                    'Hair Care',
                    'Fragrances',
                    'Grooming Tools',
                    'Personal Care Essentials',
                ],
            ],
            [
                'name' => 'Fashion Accessories',
                'children' => [
                    'Bags & Wallets',
                    'Watches',
                    'Sunglasses',
                    'Belts',
                    'Jewelry',
                ],
            ],
            [
                'name' => 'Daily Needs',
                'children' => [
                    'Household Supplies',
                    'Health Essentials',
                    'Baby Care',
                    'Stationery',
                    'Pet Supplies',
                ],
            ],
        ];

        foreach ($groups as $group) {
            $parent = $this->mainCategory($group['name'], $group['legacy'] ?? []);

            foreach ($group['children'] as $childName) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($childName)],
                    [
                        'parent_id' => $parent->id,
                        'name' => $childName,
                        'description' => null,
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    private function mainCategory(string $name, array $legacyNames = []): Category
    {
        $slug = Str::slug($name);
        $category = Category::where('slug', $slug)->first();

        foreach ($legacyNames as $legacyName) {
            $category ??= Category::where('slug', Str::slug($legacyName))
                ->orWhere('name', $legacyName)
                ->first();
        }

        if ($category) {
            $category->update([
                'parent_id' => null,
                'name' => $name,
                'slug' => $slug,
                'description' => null,
                'is_active' => true,
            ]);

            return $category;
        }

        return Category::create([
            'parent_id' => null,
            'name' => $name,
            'slug' => $slug,
            'description' => null,
            'is_active' => true,
        ]);
    }
}

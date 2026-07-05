<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ProfessionalCategorySeeder::class);
        $this->call(AtvsGenxProductSeeder::class);

        User::updateOrCreate(
            ['email' => 'admin@bonikpoint.com'],
            [
                'name' => 'Bonik Point Admin',
                'mobile' => '01700000000',
                'password' => bcrypt('password'),
                'utype' => 'adm',
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@bonikpoint.com'],
            [
                'name' => 'Demo Customer',
                'mobile' => '01800000000',
                'password' => bcrypt('password'),
                'utype' => 'usr',
            ]
        );

        $categories = collect([
            ['name' => 'Living Room', 'slug' => 'living-room'],
            ['name' => 'Bedroom', 'slug' => 'bedroom'],
            ['name' => 'Dining', 'slug' => 'dining'],
            ['name' => 'Office', 'slug' => 'office'],
        ])->mapWithKeys(fn ($category) => [
            $category['slug'] => Category::updateOrCreate(['slug' => $category['slug']], $category),
        ]);

        $products = [
            ['category' => 'living-room', 'name' => 'Elona Bedside Grey Table', 'slug' => 'elona-bedside-grey-table', 'price' => 4200, 'compare_price' => 5200, 'stock' => 18, 'image' => 'assets/images/product/product-01.jpg', 'is_featured' => true],
            ['category' => 'living-room', 'name' => 'Simple Minimal Chair', 'slug' => 'simple-minimal-chair', 'price' => 2600, 'compare_price' => 3200, 'stock' => 24, 'image' => 'assets/images/product/product-02.jpg', 'is_featured' => true],
            ['category' => 'dining', 'name' => 'Pendant Chandelier Light', 'slug' => 'pendant-chandelier-light', 'price' => 3800, 'compare_price' => null, 'stock' => 12, 'image' => 'assets/images/product/product-03.jpg', 'is_featured' => false],
            ['category' => 'bedroom', 'name' => 'High Quality Vase Bottle', 'slug' => 'high-quality-vase-bottle', 'price' => 1700, 'compare_price' => 2300, 'stock' => 30, 'image' => 'assets/images/product/product-04.jpg', 'is_featured' => true],
            ['category' => 'office', 'name' => 'Modern Accent Chair', 'slug' => 'modern-accent-chair', 'price' => 5100, 'compare_price' => null, 'stock' => 9, 'image' => 'assets/images/product/product-12.jpg', 'is_featured' => true],
            ['category' => 'living-room', 'name' => 'Herman Seater Sofa', 'slug' => 'herman-seater-sofa', 'price' => 18500, 'compare_price' => 21000, 'stock' => 7, 'image' => 'assets/images/product/product-08.jpg', 'is_featured' => false],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(['slug' => $product['slug']], [
                'category_id' => $categories[$product['category']]->id,
                'name' => $product['name'],
                'description' => 'A quality Bonik Point product ready for customer orders.',
                'price' => $product['price'],
                'compare_price' => $product['compare_price'],
                'stock' => $product['stock'],
                'sku' => strtoupper(substr($product['slug'], 0, 3)).'-'.abs(crc32($product['slug'])),
                'image' => $product['image'],
                'is_featured' => $product['is_featured'],
                'is_active' => true,
            ]);
        }

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'mobile' => '01900000000',
                'password' => bcrypt('password'),
                'utype' => 'usr',
            ]
        );
    }
}

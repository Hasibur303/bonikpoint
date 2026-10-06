<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductEngagementTest extends TestCase
{
    use RefreshDatabase;

    protected function migrateFreshUsing(): array
    {
        // The legacy guest-order migration uses MySQL-only ALTER TABLE syntax.
        return ['--realpath' => true, '--path' => array_values(array_filter(
            glob(database_path('migrations/*.php')),
            fn (string $path) => ! str_ends_with($path, '2026_07_13_030000_allow_guest_orders.php')
        ))];
    }

    private function product(): Product
    {
        $category = Category::create(['name' => 'Kitchen', 'slug' => 'kitchen', 'is_active' => true]);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Cleaner',
            'slug' => 'cleaner',
            'price' => 400,
            'stock' => 10,
            'is_active' => true,
        ]);
    }

    public function test_guest_like_is_idempotent_and_can_be_removed(): void
    {
        $product = $this->product();
        $url = route('products.like', $product);
        $this->postJson($url, ['liked' => true])->assertOk()->assertJson(['liked' => true, 'likes_count' => 1]);
        $this->postJson($url, ['liked' => true])->assertOk()->assertJson(['likes_count' => 1]);
        $this->postJson($url, ['liked' => false])->assertOk()->assertJson(['liked' => false, 'likes_count' => 0]);
        $this->assertDatabaseCount('product_likes', 0);
    }

    public function test_signed_in_likes_follow_the_account_across_sessions(): void
    {
        $product = $this->product();
        $this->actingAs(User::factory()->create());
        $this->withSession(['product_visitor' => 'first'])->postJson(route('products.like', $product), ['liked' => true])->assertOk();
        $this->withSession(['product_visitor' => 'second'])->postJson(route('products.like', $product), ['liked' => true])
            ->assertOk()->assertJson(['likes_count' => 1]);
    }

    public function test_inactive_products_cannot_be_liked(): void
    {
        $product = $this->product();
        $product->update(['is_active' => false]);
        $this->postJson(route('products.like', $product), ['liked' => true])->assertNotFound();
        $this->assertDatabaseCount('product_likes', 0);
    }

    public function test_visits_count_once_per_browser_session_and_stats_render(): void
    {
        $product = $this->product();
        $url = route('shop.show', $product);
        $this->withSession(['product_visitor' => 'first'])->get($url)->assertOk()->assertSee('1 visits');
        $this->get($url)->assertOk()->assertSee('1 visits');
        $this->withSession(['product_visitor' => 'second'])->get($url)->assertOk()->assertSee('2 visits');
        $this->assertDatabaseCount('product_visits', 2);
        $this->get(route('shop.index'))->assertOk()->assertSee('2 visits');
    }

    public function test_sold_quantity_only_includes_delivered_items(): void
    {
        $product = $this->product();
        $user = User::factory()->create();
        foreach (['delivered' => 3, 'pending' => 4, 'cancelled' => 5, 'confirmed' => 2] as $status => $quantity) {
            $order = Order::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'order_number' => 'TEST-'.$status,
                'customer_name' => 'Customer',
                'mobile' => '01712345678',
                'address' => 'Gulshan',
                'city' => 'Dhaka',
                'status' => $status,
                'subtotal' => 400 * $quantity,
                'shipping' => 0,
                'total' => 400 * $quantity,
            ]);
            $order->items()->create([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'unit_price' => 400,
                'quantity' => $quantity,
                'total' => 400 * $quantity,
            ]);
        }

        $this->get(route('shop.show', $product))->assertOk()->assertSee('3 sold');
    }
}

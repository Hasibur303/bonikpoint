<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontProductEditorTest extends TestCase
{
    use RefreshDatabase;

    protected function migrateFreshUsing(): array
    {
        return ['--realpath' => true, '--path' => array_values(array_filter(
            glob(database_path('migrations/*.php')),
            fn (string $path) => ! str_ends_with($path, '2026_07_13_030000_allow_guest_orders.php')
        ))];
    }

    private function product(): Product
    {
        $category = Category::create(['name' => 'Kitchen', 'slug' => 'kitchen']);

        return Product::create([
            'category_id' => $category->id, 'name' => 'Cleaner', 'slug' => 'cleaner',
            'price' => 400, 'stock' => 10, 'warranty_type' => 'none', 'is_active' => true,
        ]);
    }

    private function admin(): User
    {
        return User::factory()->create(['utype' => 'adm']);
    }

    private function changes(Product $product): array
    {
        return [
            'name' => 'Updated cleaner', 'category_id' => $product->category_id,
            'description' => 'Updated description', 'price' => 500, 'buying_price' => 250,
            'stock' => 12, 'warranty_type' => 'none', 'is_active' => 1, 'is_featured' => 1,
            'colors' => [['name' => 'Blue', 'hex_code' => '#0000FF']],
        ];
    }

    public function test_guests_and_customers_cannot_enable_editing_or_change_products(): void
    {
        $product = $this->product();
        $this->post(route('admin.storefront.edit-mode'), ['enabled' => 1])->assertRedirect(route('login'));
        $this->get(route('admin.storefront.products.edit', $product))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create(['utype' => 'usr']))
            ->withSession(['storefront_edit_mode' => true]);
        $this->post(route('admin.storefront.edit-mode'), ['enabled' => 1])->assertForbidden();
        $this->get(route('admin.storefront.products.edit', $product))->assertForbidden();
        $this->put(route('admin.storefront.products.update', $product), $this->changes($product))->assertForbidden();
        $this->get(route('shop.show', $product))->assertOk()->assertDontSee('data-storefront-product-edit=', false);
        $this->assertEquals(400, $product->fresh()->price);
    }

    public function test_mode_is_off_by_default_and_admin_can_toggle_it(): void
    {
        $product = $this->product();
        $this->actingAs($this->admin());
        $this->get(route('shop.show', $product))->assertOk()->assertSee('Storefront edit mode')
            ->assertDontSee('data-storefront-product-edit=', false);
        $this->get(route('admin.storefront.products.edit', $product))->assertForbidden();
        $this->put(route('admin.storefront.products.update', $product), $this->changes($product))->assertForbidden();
        $this->from(route('shop.index'))->post(route('admin.storefront.edit-mode'), ['enabled' => 1])
            ->assertRedirect(route('shop.index'))->assertSessionHas('storefront_edit_mode', true);
        $this->get(route('shop.index'))->assertOk()->assertSee('data-storefront-product-edit=', false);
        $this->get(route('admin.storefront.products.edit', $product))->assertOk()
            ->assertSee(route('admin.storefront.products.update', $product), false)
            ->assertSee('name="buying_price"', false)->assertSee('name="gallery_images[]"', false);
        $this->post(route('admin.storefront.edit-mode'), ['enabled' => 0])->assertSessionHas('storefront_edit_mode', false);
        $this->get(route('admin.storefront.products.edit', $product))->assertForbidden();
    }

    public function test_storefront_saves_details_and_returns_to_editor_after_slug_changes(): void
    {
        $product = $this->product();
        $this->actingAs($this->admin())->withSession(['storefront_edit_mode' => true]);
        $response = $this->put(route('admin.storefront.products.update', $product), $this->changes($product));
        $product->refresh();
        $response->assertRedirect(route('admin.storefront.products.edit', $product))
            ->assertSessionHas('success', 'Product updated.')
            ->assertSessionHas('storefront_product_url', route('shop.show', $product));
        $this->assertEquals(500, $product->price);
        $this->assertEquals(12, $product->stock);
        $this->assertEquals('Updated description', $product->description);
        $this->assertDatabaseHas('product_colors', ['product_id' => $product->id, 'name' => 'Blue']);
        $this->get(route('admin.storefront.products.edit', $product))->assertOk()->assertSee('Product updated.');
    }

    public function test_invalid_values_return_errors_without_saving(): void
    {
        $product = $this->product();
        $this->actingAs($this->admin())->withSession(['storefront_edit_mode' => true]);
        $url = route('admin.storefront.products.edit', $product);
        $this->from($url)->put(route('admin.storefront.products.update', $product), array_replace($this->changes($product), ['price' => -1]))
            ->assertRedirect($url)->assertSessionHasErrors('price');
        $this->assertEquals(400, $product->fresh()->price);
        $this->get($url)->assertOk()->assertSee('The product was not saved.');
    }

    public function test_storefront_can_edit_faqs_and_admin_panel_keeps_its_redirect(): void
    {
        $product = $this->product();
        $this->actingAs($this->admin())->withSession(['storefront_edit_mode' => true]);
        $this->get(route('admin.storefront.products.faqs.edit', $product))->assertOk();
        $this->patch(route('admin.storefront.products.faqs.update', $product), [
            'faqs' => [['question' => 'Delivery?', 'answer' => 'Nationwide.']],
        ])->assertRedirect(route('admin.storefront.products.faqs.edit', $product));
        $this->assertDatabaseHas('product_faqs', ['product_id' => $product->id, 'question' => 'Delivery?']);
        $this->put(route('admin.products.update', $product), $this->changes($product))->assertRedirect(route('admin.products.index'));
    }
}

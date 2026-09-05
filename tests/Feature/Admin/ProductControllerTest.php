<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('admin.products.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_stylists_cannot_access(): void
    {
        $this->actingAs($this->stylistUser());
        $response = $this->get(route('admin.products.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_index(): void
    {
        Product::factory()->count(3)->create();
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.products.index'));
        $response->assertOk();
    }

    public function test_admin_can_create_product(): void
    {
        $category = ProductCategory::factory()->create();
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.products.store'), [
            'product_category_id' => $category->id,
            'name' => 'Shampoo Reparador',
            'cost' => 50,
            'price' => 150,
            'commission_percentage' => 10,
            'min_stock' => 5,
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'name' => 'Shampoo Reparador',
            'product_category_id' => $category->id,
        ]);
    }

    public function test_store_generates_sku_when_missing(): void
    {
        $category = ProductCategory::factory()->create();
        $this->actingAs($this->admin());

        $this->post(route('admin.products.store'), [
            'product_category_id' => $category->id,
            'name' => 'Producto Sin SKU',
            'cost' => 10,
            'price' => 20,
            'commission_percentage' => 10,
            'min_stock' => 5,
        ]);

        $product = Product::where('name', 'Producto Sin SKU')->first();
        $this->assertNotNull($product);
        $this->assertNotEmpty($product->sku);
    }

    public function test_admin_can_update_product(): void
    {
        $product = Product::factory()->create(['name' => 'Original', 'price' => 100]);
        $this->actingAs($this->admin());

        $response = $this->put(route('admin.products.update', $product), [
            'product_category_id' => $product->product_category_id,
            'name' => 'Actualizado',
            'cost' => 20,
            'price' => 200,
            'commission_percentage' => 15,
            'min_stock' => 3,
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Actualizado',
            'price' => 200,
        ]);
    }

    public function test_admin_can_delete_product(): void
    {
        $product = Product::factory()->create();
        $this->actingAs($this->admin());

        $response = $this->delete(route('admin.products.destroy', $product));

        $response->assertRedirect();
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}

<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class ProductCategoryControllerTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('admin.product-categories.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_stylists_cannot_access(): void
    {
        $this->actingAs($this->stylistUser());
        $response = $this->get(route('admin.product-categories.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_index(): void
    {
        ProductCategory::factory()->count(3)->create();
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.product-categories.index'));
        $response->assertOk();
    }

    public function test_admin_can_create_category(): void
    {
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.product-categories.store'), [
            'name' => 'Shampoos',
            'icon' => '🧴',
            'description' => 'Shampoos profesionales',
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('product_categories', [
            'name' => 'Shampoos',
            'slug' => 'shampoos',
        ]);
    }

    public function test_store_requires_name(): void
    {
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.product-categories.store'), [
            'icon' => '🧴',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('product_categories', 0);
    }

    public function test_admin_can_update_category(): void
    {
        $category = ProductCategory::factory()->create(['name' => 'Original']);
        $this->actingAs($this->admin());

        $response = $this->put(route('admin.product-categories.update', $category), [
            'name' => 'Actualizada',
            'icon' => '📦',
            'description' => 'Nueva descripción',
            'is_active' => false,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('product_categories', [
            'id' => $category->id,
            'name' => 'Actualizada',
            'is_active' => false,
        ]);
    }

    public function test_admin_can_delete_empty_category(): void
    {
        $category = ProductCategory::factory()->create();
        $this->actingAs($this->admin());

        $response = $this->delete(route('admin.product-categories.destroy', $category));

        $response->assertRedirect();
        $this->assertDatabaseMissing('product_categories', ['id' => $category->id]);
    }

    public function test_cannot_delete_category_with_products(): void
    {
        $category = ProductCategory::factory()->create();
        Product::factory()->create(['product_category_id' => $category->id]);
        $this->actingAs($this->admin());

        $response = $this->delete(route('admin.product-categories.destroy', $category));

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('product_categories', ['id' => $category->id]);
    }
}

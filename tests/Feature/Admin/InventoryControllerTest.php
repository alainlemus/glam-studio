<?php

namespace Tests\Feature\Admin;

use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class InventoryControllerTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('admin.inventory.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_stylists_cannot_access(): void
    {
        $this->actingAs($this->stylistUser());
        $response = $this->get(route('admin.inventory.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_index(): void
    {
        ProductStock::factory()->count(3)->create();
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.inventory.index'));
        $response->assertOk();
    }

    public function test_admin_can_adjust_stock_up(): void
    {
        $branch = Branch::factory()->create();
        $product = Product::factory()->create(['min_stock' => 5]);
        ProductStock::factory()->create(['product_id' => $product->id, 'branch_id' => $branch->id, 'stock' => 10]);
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.inventory.adjust', [$product, $branch]), [
            'adjustment' => 5,
            'reason' => 'Reabastecimiento',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('product_stocks', [
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'stock' => 15,
        ]);
    }

    public function test_stock_cannot_go_below_zero(): void
    {
        $branch = Branch::factory()->create();
        $product = Product::factory()->create();
        ProductStock::factory()->create(['product_id' => $product->id, 'branch_id' => $branch->id, 'stock' => 3]);
        $this->actingAs($this->admin());

        $this->post(route('admin.inventory.adjust', [$product, $branch]), [
            'adjustment' => -10,
        ]);

        $this->assertDatabaseHas('product_stocks', [
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'stock' => 0,
        ]);
    }

    public function test_admin_can_transfer_stock_between_branches(): void
    {
        $product = Product::factory()->create();
        $from = Branch::factory()->create();
        $to = Branch::factory()->create();
        ProductStock::factory()->create(['product_id' => $product->id, 'branch_id' => $from->id, 'stock' => 20]);
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.inventory.transfer'), [
            'product_id' => $product->id,
            'from_branch_id' => $from->id,
            'to_branch_id' => $to->id,
            'quantity' => 5,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('product_stocks', ['product_id' => $product->id, 'branch_id' => $from->id, 'stock' => 15]);
        $this->assertDatabaseHas('product_stocks', ['product_id' => $product->id, 'branch_id' => $to->id, 'stock' => 5]);
    }

    public function test_transfer_fails_with_insufficient_stock(): void
    {
        $product = Product::factory()->create();
        $from = Branch::factory()->create();
        $to = Branch::factory()->create();
        ProductStock::factory()->create(['product_id' => $product->id, 'branch_id' => $from->id, 'stock' => 2]);
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.inventory.transfer'), [
            'product_id' => $product->id,
            'from_branch_id' => $from->id,
            'to_branch_id' => $to->id,
            'quantity' => 5,
        ]);

        $response->assertSessionHasErrors('quantity');
        $this->assertDatabaseHas('product_stocks', ['product_id' => $product->id, 'branch_id' => $from->id, 'stock' => 2]);
    }

    public function test_manager_cannot_transfer_stock(): void
    {
        $product = Product::factory()->create();
        $from = Branch::factory()->create();
        $to = Branch::factory()->create();
        ProductStock::factory()->create(['product_id' => $product->id, 'branch_id' => $from->id, 'stock' => 10]);
        $this->actingAs($this->manager($from));

        $response = $this->post(route('admin.inventory.transfer'), [
            'product_id' => $product->id,
            'from_branch_id' => $from->id,
            'to_branch_id' => $to->id,
            'quantity' => 5,
        ]);

        $response->assertForbidden();
    }
}

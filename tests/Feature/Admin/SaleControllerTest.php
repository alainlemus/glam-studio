<?php

namespace Tests\Feature\Admin;

use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Sale;
use App\Models\Service;
use App\Models\Stylist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class SaleControllerTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('admin.sales.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_stylists_cannot_access(): void
    {
        $this->actingAs($this->stylistUser());
        $response = $this->get(route('admin.sales.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_index(): void
    {
        Sale::factory()->count(3)->create();
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.sales.index'));
        $response->assertOk();
    }

    public function test_stylists_cannot_export(): void
    {
        $this->actingAs($this->stylistUser());
        $response = $this->get(route('admin.sales.export'));
        $response->assertForbidden();
    }

    public function test_admin_can_export_csv(): void
    {
        Sale::factory()->create(['ticket_number' => 'V-TEST-001']);
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.sales.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Ticket', $response->streamedContent());
        $this->assertStringContainsString('V-TEST-001', $response->streamedContent());
    }

    public function test_export_respects_branch_scope(): void
    {
        $ownBranch = Branch::factory()->create();
        $otherBranch = Branch::factory()->create();
        Sale::factory()->create(['branch_id' => $ownBranch->id, 'ticket_number' => 'V-OWN-001']);
        Sale::factory()->create(['branch_id' => $otherBranch->id, 'ticket_number' => 'V-OTHER-002']);
        $this->actingAs($this->manager($ownBranch));

        $content = $this->get(route('admin.sales.export'))->streamedContent();

        $this->assertStringContainsString('V-OWN-001', $content);
        $this->assertStringNotContainsString('V-OTHER-002', $content);
    }

    public function test_admin_can_register_service_sale_and_creates_income_and_commission(): void
    {
        $branch = Branch::factory()->create();
        $stylist = Stylist::factory()->create(['branch_id' => $branch->id]);
        $service = Service::factory()->create(['price' => 300, 'commission_percentage' => 25]);
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.sales.store'), [
            'branch_id' => $branch->id,
            'stylist_id' => $stylist->id,
            'payment_method' => 'cash',
            'items' => [
                ['type' => 'service', 'id' => $service->id, 'quantity' => 1],
            ],
        ]);

        $response->assertRedirect();
        $sale = Sale::first();
        $this->assertNotNull($sale);
        $this->assertEquals(300, $sale->total);
        $this->assertDatabaseHas('incomes', ['sale_id' => $sale->id, 'amount' => 300]);
        $this->assertDatabaseHas('commissions', ['sale_id' => $sale->id, 'stylist_id' => $stylist->id, 'amount' => 75]);
    }

    public function test_admin_can_register_product_sale_and_decrements_stock(): void
    {
        $branch = Branch::factory()->create();
        $product = Product::factory()->create(['price' => 100]);
        ProductStock::factory()->create(['product_id' => $product->id, 'branch_id' => $branch->id, 'stock' => 10]);
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.sales.store'), [
            'branch_id' => $branch->id,
            'payment_method' => 'cash',
            'items' => [
                ['type' => 'product', 'id' => $product->id, 'quantity' => 2],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('product_stocks', ['product_id' => $product->id, 'branch_id' => $branch->id, 'stock' => 8]);
    }

    public function test_manager_cannot_register_sale_for_other_branch(): void
    {
        $ownBranch = Branch::factory()->create();
        $otherBranch = Branch::factory()->create();
        $service = Service::factory()->create();
        $this->actingAs($this->manager($ownBranch));

        $response = $this->post(route('admin.sales.store'), [
            'branch_id' => $otherBranch->id,
            'payment_method' => 'cash',
            'items' => [
                ['type' => 'service', 'id' => $service->id, 'quantity' => 1],
            ],
        ]);

        $response->assertForbidden();
    }

    public function test_admin_can_view_sale_show(): void
    {
        $sale = Sale::factory()->create();
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.sales.show', $sale));
        $response->assertOk();
    }

    public function test_admin_can_view_ticket(): void
    {
        $sale = Sale::factory()->create();
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.sales.ticket', $sale));
        $response->assertOk();
    }

    public function test_admin_can_cancel_sale(): void
    {
        $sale = Sale::factory()->create(['status' => 'paid']);
        $this->actingAs($this->admin());

        $response = $this->delete(route('admin.sales.destroy', $sale));

        $response->assertRedirect();
        $this->assertDatabaseHas('sales', ['id' => $sale->id, 'status' => 'cancelled']);
    }
}

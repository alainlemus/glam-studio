<?php

namespace Tests\Feature\Admin;

use App\Models\Branch;
use App\Models\Commission;
use App\Models\Stylist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class CommissionControllerTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('admin.commissions.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_stylists_cannot_access(): void
    {
        $this->actingAs($this->stylistUser());
        $response = $this->get(route('admin.commissions.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_index(): void
    {
        Commission::factory()->count(3)->create();
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.commissions.index'));
        $response->assertOk();
    }

    public function test_admin_can_export_csv(): void
    {
        Commission::factory()->create();
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.commissions.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Estilista', $response->streamedContent());
    }

    public function test_export_respects_branch_scope(): void
    {
        $ownBranch = Branch::factory()->create();
        $otherBranch = Branch::factory()->create();
        $ownStylist = Stylist::factory()->create(['branch_id' => $ownBranch->id]);
        $otherStylist = Stylist::factory()->create(['branch_id' => $otherBranch->id]);
        Commission::factory()->create(['stylist_id' => $ownStylist->id, 'amount' => 111]);
        Commission::factory()->create(['stylist_id' => $otherStylist->id, 'amount' => 222]);
        $this->actingAs($this->manager($ownBranch));

        $content = $this->get(route('admin.commissions.export'))->streamedContent();

        $this->assertStringContainsString('111.00', $content);
        $this->assertStringNotContainsString('222.00', $content);
    }

    public function test_admin_can_pay_single_commission(): void
    {
        $commission = Commission::factory()->create(['status' => 'pending']);
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.commissions.pay', $commission));

        $response->assertRedirect();
        $commission->refresh();
        $this->assertEquals('paid', $commission->status);
        $this->assertNotNull($commission->paid_at);
    }

    public function test_manager_cannot_pay_commission_from_other_branch(): void
    {
        $branch = Branch::factory()->create();
        $stylist = Stylist::factory()->create();
        $commission = Commission::factory()->create(['stylist_id' => $stylist->id, 'status' => 'pending']);
        $this->actingAs($this->manager($branch));

        $response = $this->post(route('admin.commissions.pay', $commission));

        $response->assertForbidden();
    }

    public function test_admin_can_pay_batch_and_creates_salary(): void
    {
        $stylist = Stylist::factory()->create(['base_salary' => 8000]);
        Commission::factory()->create(['stylist_id' => $stylist->id, 'status' => 'pending', 'amount' => 100]);
        Commission::factory()->create(['stylist_id' => $stylist->id, 'status' => 'pending', 'amount' => 50]);
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.commissions.pay_batch'), [
            'stylist_id' => $stylist->id,
            'from' => now()->subDay()->format('Y-m-d'),
            'to' => now()->addDay()->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('salaries', ['stylist_id' => $stylist->id, 'commissions_total' => 150, 'total' => 8150]);
        $this->assertEquals(0, Commission::where('stylist_id', $stylist->id)->where('status', 'pending')->count());
    }

    public function test_pay_batch_fails_when_no_pending_commissions(): void
    {
        $stylist = Stylist::factory()->create();
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.commissions.pay_batch'), [
            'stylist_id' => $stylist->id,
            'from' => now()->subDay()->format('Y-m-d'),
            'to' => now()->addDay()->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseCount('salaries', 0);
    }
}

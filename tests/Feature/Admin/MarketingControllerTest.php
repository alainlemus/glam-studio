<?php

namespace Tests\Feature\Admin;

use App\Models\Branch;
use App\Models\MarketingCampaign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class MarketingControllerTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('admin.marketing.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_stylists_cannot_access(): void
    {
        $this->actingAs($this->stylistUser());
        $response = $this->get(route('admin.marketing.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_index(): void
    {
        MarketingCampaign::factory()->count(3)->create();
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.marketing.index'));
        $response->assertOk();
    }

    public function test_admin_can_create_campaign(): void
    {
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.marketing.store'), [
            'name' => 'Promo Verano',
            'type' => 'whatsapp',
            'start_date' => now()->format('Y-m-d'),
            'status' => 'draft',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('marketing_campaigns', ['name' => 'Promo Verano']);
    }

    public function test_admin_can_update_campaign(): void
    {
        $campaign = MarketingCampaign::factory()->create(['name' => 'Original']);
        $this->actingAs($this->admin());

        $response = $this->put(route('admin.marketing.update', $campaign), [
            'name' => 'Actualizada',
            'type' => 'whatsapp',
            'start_date' => now()->format('Y-m-d'),
            'status' => 'draft',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('marketing_campaigns', ['id' => $campaign->id, 'name' => 'Actualizada']);
    }

    public function test_admin_can_activate_campaign(): void
    {
        $campaign = MarketingCampaign::factory()->create(['status' => 'draft']);
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.marketing.activate', $campaign));

        $response->assertRedirect();
        $this->assertDatabaseHas('marketing_campaigns', ['id' => $campaign->id, 'status' => 'active']);
    }

    public function test_admin_can_delete_campaign(): void
    {
        $campaign = MarketingCampaign::factory()->create();
        $this->actingAs($this->admin());

        $response = $this->delete(route('admin.marketing.destroy', $campaign));

        $response->assertRedirect();
        $this->assertDatabaseMissing('marketing_campaigns', ['id' => $campaign->id]);
    }

    public function test_manager_cannot_modify_global_campaign(): void
    {
        $branch = Branch::factory()->create();
        $campaign = MarketingCampaign::factory()->create(['branch_id' => null]);
        $this->actingAs($this->manager($branch));

        $response = $this->delete(route('admin.marketing.destroy', $campaign));

        $response->assertForbidden();
    }

    public function test_manager_can_modify_own_branch_campaign(): void
    {
        $branch = Branch::factory()->create();
        $campaign = MarketingCampaign::factory()->create(['branch_id' => $branch->id]);
        $this->actingAs($this->manager($branch));

        $response = $this->delete(route('admin.marketing.destroy', $campaign));

        $response->assertRedirect();
        $this->assertDatabaseMissing('marketing_campaigns', ['id' => $campaign->id]);
    }
}

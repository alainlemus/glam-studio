<?php

namespace Tests\Feature\Admin;

use App\Models\LoyaltyCard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class LoyaltyControllerTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('admin.loyalty.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_stylists_cannot_access(): void
    {
        $this->actingAs($this->stylistUser());
        $response = $this->get(route('admin.loyalty.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_index(): void
    {
        LoyaltyCard::factory()->count(3)->create();
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.loyalty.index'));
        $response->assertOk();
    }

    public function test_admin_can_add_stamp(): void
    {
        $card = LoyaltyCard::factory()->create(['stamps_current' => 2]);
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.loyalty.add_stamp', $card), ['quantity' => 3]);

        $response->assertRedirect();
        $this->assertDatabaseHas('loyalty_cards', ['id' => $card->id, 'stamps_current' => 5]);
        $this->assertDatabaseHas('loyalty_stamps', ['loyalty_card_id' => $card->id, 'type' => 'earned', 'quantity' => 3]);
    }

    public function test_admin_can_redeem_reward_when_complete(): void
    {
        $card = LoyaltyCard::factory()->create(['stamps_current' => 10, 'stamps_required' => 10]);
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.loyalty.redeem', $card));

        $response->assertRedirect();
        $this->assertDatabaseHas('loyalty_cards', ['id' => $card->id, 'stamps_current' => 0, 'total_rewards_claimed' => 1]);
    }

    public function test_cannot_redeem_incomplete_card(): void
    {
        $card = LoyaltyCard::factory()->create(['stamps_current' => 3, 'stamps_required' => 10]);
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.loyalty.redeem', $card));

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('loyalty_cards', ['id' => $card->id, 'stamps_current' => 3]);
    }
}

<?php

namespace Tests\Feature\Admin;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class ClientControllerTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('admin.clients.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_stylists_cannot_access(): void
    {
        $this->actingAs($this->stylistUser());
        $response = $this->get(route('admin.clients.index'));
        $response->assertForbidden();
    }

    public function test_receptionist_can_access(): void
    {
        $this->actingAs($this->receptionist());
        $response = $this->get(route('admin.clients.index'));
        $response->assertOk();
    }

    public function test_admin_can_view_index(): void
    {
        Client::factory()->count(3)->create();
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.clients.index'));
        $response->assertOk();
    }

    public function test_admin_can_create_client_with_loyalty_card(): void
    {
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.clients.store'), [
            'name' => 'Ana García',
            'phone' => '5512345678',
            'email' => 'ana@example.com',
        ]);

        $response->assertRedirect();
        $client = Client::where('phone', '5512345678')->first();
        $this->assertNotNull($client);
        $this->assertNotNull($client->loyaltyCard);
    }

    public function test_store_requires_unique_phone(): void
    {
        Client::factory()->create(['phone' => '5512345678']);
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.clients.store'), [
            'name' => 'Otra persona',
            'phone' => '5512345678',
        ]);

        $response->assertSessionHasErrors('phone');
    }

    public function test_admin_can_view_edit_form(): void
    {
        $client = Client::factory()->create();
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.clients.edit', $client));
        $response->assertOk();
    }

    public function test_admin_can_update_client(): void
    {
        $client = Client::factory()->create(['name' => 'Original']);
        $this->actingAs($this->admin());

        $response = $this->put(route('admin.clients.update', $client), [
            'name' => 'Actualizada',
            'phone' => $client->phone,
            'is_active' => true,
            'is_blocked' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('clients', ['id' => $client->id, 'name' => 'Actualizada', 'is_blocked' => true]);
    }

    public function test_admin_can_delete_client(): void
    {
        $client = Client::factory()->create();
        $this->actingAs($this->admin());

        $response = $this->delete(route('admin.clients.destroy', $client));

        $response->assertRedirect();
        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }

    public function test_manager_can_view_client_with_appointment_in_their_branch(): void
    {
        $branch = Branch::factory()->create();
        $client = Client::factory()->create();
        Appointment::factory()->create(['client_id' => $client->id, 'branch_id' => $branch->id]);
        $this->actingAs($this->manager($branch));

        $response = $this->get(route('admin.clients.show', $client));
        $response->assertOk();
    }

    public function test_manager_cannot_view_client_without_appointment_in_their_branch(): void
    {
        $branch = Branch::factory()->create();
        $otherBranch = Branch::factory()->create();
        $client = Client::factory()->create();
        Appointment::factory()->create(['client_id' => $client->id, 'branch_id' => $otherBranch->id]);
        $this->actingAs($this->manager($branch));

        $response = $this->get(route('admin.clients.show', $client));
        $response->assertForbidden();
    }
}

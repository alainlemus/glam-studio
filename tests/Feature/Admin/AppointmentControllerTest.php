<?php

namespace Tests\Feature\Admin;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class AppointmentControllerTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('admin.appointments.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_stylists_cannot_access(): void
    {
        $this->actingAs($this->stylistUser());
        $response = $this->get(route('admin.appointments.index'));
        $response->assertForbidden();
    }

    public function test_receptionist_can_access(): void
    {
        $this->actingAs($this->receptionist());
        $response = $this->get(route('admin.appointments.index'));
        $response->assertOk();
    }

    public function test_admin_can_create_appointment(): void
    {
        $client = Client::factory()->create();
        $branch = Branch::factory()->create();
        $service = Service::factory()->create(['price' => 300, 'duration_minutes' => 45]);
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.appointments.store'), [
            'client_id' => $client->id,
            'branch_id' => $branch->id,
            'date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '10:00',
            'services' => [['id' => $service->id]],
            'status' => 'pending',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', ['client_id' => $client->id, 'branch_id' => $branch->id, 'total' => 300]);
    }

    public function test_manager_cannot_create_appointment_for_other_branch(): void
    {
        $ownBranch = Branch::factory()->create();
        $otherBranch = Branch::factory()->create();
        $client = Client::factory()->create();
        $service = Service::factory()->create();
        $this->actingAs($this->manager($ownBranch));

        $response = $this->post(route('admin.appointments.store'), [
            'client_id' => $client->id,
            'branch_id' => $otherBranch->id,
            'date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '10:00',
            'services' => [['id' => $service->id]],
            'status' => 'pending',
        ]);

        $response->assertForbidden();
    }

    public function test_admin_can_confirm_appointment(): void
    {
        $appointment = Appointment::factory()->create(['status' => 'pending']);
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.appointments.confirm', $appointment));

        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'confirmed']);
    }

    public function test_admin_can_cancel_appointment(): void
    {
        $appointment = Appointment::factory()->create(['status' => 'pending']);
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.appointments.cancel', $appointment), ['reason' => 'Cliente canceló']);

        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'cancelled', 'cancellation_reason' => 'Cliente canceló']);
    }

    public function test_admin_can_complete_appointment(): void
    {
        $appointment = Appointment::factory()->create(['status' => 'confirmed']);
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.appointments.complete', $appointment));

        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'completed']);
    }

    public function test_admin_can_mark_no_show_and_it_increments_client_count(): void
    {
        $client = Client::factory()->create(['no_show_count' => 0]);
        $appointment = Appointment::factory()->create(['client_id' => $client->id, 'status' => 'confirmed']);
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.appointments.no_show', $appointment));

        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'no_show']);
        $this->assertDatabaseHas('clients', ['id' => $client->id, 'no_show_count' => 1]);
    }

    public function test_manager_cannot_act_on_appointment_from_other_branch(): void
    {
        $ownBranch = Branch::factory()->create();
        $otherBranch = Branch::factory()->create();
        $appointment = Appointment::factory()->create(['branch_id' => $otherBranch->id, 'status' => 'pending']);
        $this->actingAs($this->manager($ownBranch));

        $response = $this->post(route('admin.appointments.confirm', $appointment));

        $response->assertForbidden();
    }

    public function test_admin_can_delete_appointment(): void
    {
        $appointment = Appointment::factory()->create();
        $this->actingAs($this->admin());

        $response = $this->delete(route('admin.appointments.destroy', $appointment));

        $response->assertRedirect();
        $this->assertDatabaseMissing('appointments', ['id' => $appointment->id]);
    }
}

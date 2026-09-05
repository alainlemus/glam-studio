<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_admins_are_redirected_to_the_admin_dashboard()
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_receptionists_are_redirected_to_the_appointments_calendar()
    {
        $user = User::factory()->create(['role' => User::ROLE_RECEPTIONIST]);
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('admin.appointments.calendar'));
    }

    public function test_stylists_are_redirected_to_their_profile()
    {
        $user = User::factory()->create(['role' => User::ROLE_STYLIST]);
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('profile.edit'));
    }
}

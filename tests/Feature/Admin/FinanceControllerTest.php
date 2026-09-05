<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class FinanceControllerTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('admin.finance.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_stylists_cannot_access(): void
    {
        $this->actingAs($this->stylistUser());
        $response = $this->get(route('admin.finance.index'));
        $response->assertForbidden();
    }

    public function test_receptionist_cannot_access(): void
    {
        $this->actingAs($this->receptionist());
        $response = $this->get(route('admin.finance.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_index(): void
    {
        $this->actingAs($this->admin());
        $response = $this->get(route('admin.finance.index'));
        $response->assertOk();
    }

    public function test_manager_can_view_index(): void
    {
        $this->actingAs($this->manager());
        $response = $this->get(route('admin.finance.index'));
        $response->assertOk();
    }
}

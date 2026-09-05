<?php

namespace Tests\Feature\Admin;

use App\Models\Branch;
use App\Models\Stylist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class StylistControllerTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('admin.stylists.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_stylists_cannot_access(): void
    {
        $this->actingAs($this->stylistUser());
        $response = $this->get(route('admin.stylists.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_index(): void
    {
        Stylist::factory()->count(3)->create();
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.stylists.index'));
        $response->assertOk();
    }

    public function test_admin_can_create_stylist(): void
    {
        $branch = Branch::factory()->create();
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.stylists.store'), [
            'name' => 'Nueva Estilista',
            'email' => 'nueva@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'branch_id' => $branch->id,
            'base_salary' => 8000,
            'service_commission' => 25,
            'product_commission' => 10,
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'nueva@example.com', 'role' => 'stylist']);
        $this->assertDatabaseHas('stylists', ['branch_id' => $branch->id]);
    }

    public function test_manager_cannot_create_stylist_for_other_branch(): void
    {
        $ownBranch = Branch::factory()->create();
        $otherBranch = Branch::factory()->create();
        $this->actingAs($this->manager($ownBranch));

        $response = $this->post(route('admin.stylists.store'), [
            'name' => 'Nueva Estilista',
            'email' => 'nueva@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'branch_id' => $otherBranch->id,
            'base_salary' => 8000,
            'service_commission' => 25,
            'product_commission' => 10,
        ]);

        $response->assertForbidden();
    }

    public function test_admin_can_update_stylist(): void
    {
        $stylist = Stylist::factory()->create();
        $stylist->load('user');
        $this->actingAs($this->admin());

        $response = $this->put(route('admin.stylists.update', $stylist), [
            'name' => 'Nombre Actualizado',
            'email' => $stylist->user->email,
            'branch_id' => $stylist->branch_id,
            'base_salary' => 9000,
            'service_commission' => 30,
            'product_commission' => 12,
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $stylist->user_id, 'name' => 'Nombre Actualizado']);
        $this->assertDatabaseHas('stylists', ['id' => $stylist->id, 'base_salary' => 9000]);
    }

    public function test_admin_can_delete_stylist(): void
    {
        $stylist = Stylist::factory()->create();
        $this->actingAs($this->admin());

        $response = $this->delete(route('admin.stylists.destroy', $stylist));

        $response->assertRedirect();
        $this->assertDatabaseMissing('stylists', ['id' => $stylist->id]);
        $this->assertDatabaseMissing('users', ['id' => $stylist->user_id]);
    }
}

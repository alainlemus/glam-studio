<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('admin.users.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_managers_cannot_access(): void
    {
        $this->actingAs($this->manager());
        $response = $this->get(route('admin.users.index'));
        $response->assertForbidden();
    }

    public function test_receptionists_cannot_access(): void
    {
        $this->actingAs($this->receptionist());
        $response = $this->get(route('admin.users.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_index(): void
    {
        $this->actingAs($this->admin());
        $response = $this->get(route('admin.users.index'));
        $response->assertOk();
    }

    public function test_admin_can_create_manager(): void
    {
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.users.store'), [
            'name' => 'Nuevo Gerente',
            'email' => 'gerente@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'manager',
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'gerente@example.com', 'role' => 'manager']);
    }

    public function test_cannot_create_user_with_stylist_role(): void
    {
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.users.store'), [
            'name' => 'Intento',
            'email' => 'intento@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'stylist',
        ]);

        $response->assertSessionHasErrors('role');
    }

    public function test_admin_can_update_user(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_MANAGER, 'name' => 'Original']);
        $this->actingAs($this->admin());

        $response = $this->put(route('admin.users.update', $user), [
            'name' => 'Actualizado',
            'email' => $user->email,
            'role' => 'manager',
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Actualizado']);
    }

    public function test_admin_cannot_remove_their_own_admin_role(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        $response = $this->put(route('admin.users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'manager',
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'role' => 'admin']);
    }

    public function test_admin_cannot_deactivate_their_own_account(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        $response = $this->put(route('admin.users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'admin',
            'is_active' => false,
        ]);

        $response->assertSessionHasErrors('is_active');
    }

    public function test_admin_can_delete_other_user(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $this->actingAs($this->admin());

        $response = $this->delete(route('admin.users.destroy', $user));

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        $response = $this->delete(route('admin.users.destroy', $admin));

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_cannot_edit_a_stylist_from_this_screen(): void
    {
        $stylist = $this->stylistUser();
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.users.edit', $stylist));

        $response->assertNotFound();
    }
}

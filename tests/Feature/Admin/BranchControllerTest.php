<?php

namespace Tests\Feature\Admin;

use App\Models\Branch;
use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class BranchControllerTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('admin.branches.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_stylists_cannot_access(): void
    {
        $this->actingAs($this->stylistUser());
        $response = $this->get(route('admin.branches.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_index(): void
    {
        Branch::factory()->count(3)->create();
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.branches.index'));
        $response->assertOk();
    }

    public function test_manager_can_only_view_own_branch(): void
    {
        $own = Branch::factory()->create();
        Branch::factory()->create();
        $this->actingAs($this->manager($own));

        $response = $this->get(route('admin.branches.show', $own));
        $response->assertOk();
    }

    public function test_manager_cannot_view_other_branch(): void
    {
        $own = Branch::factory()->create();
        $other = Branch::factory()->create();
        $this->actingAs($this->manager($own));

        $response = $this->get(route('admin.branches.show', $other));
        $response->assertForbidden();
    }

    public function test_only_admin_can_create_branch(): void
    {
        $city = City::factory()->create();
        $this->actingAs($this->manager());

        $response = $this->post(route('admin.branches.store'), [
            'city_id' => $city->id,
            'name' => 'Sucursal Nueva',
            'address' => 'Calle Falsa 123',
            'phone' => '5512345678',
            'opening_time' => '09:00',
            'closing_time' => '20:00',
        ]);

        $response->assertForbidden();
    }

    public function test_admin_can_create_branch(): void
    {
        $city = City::factory()->create();
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.branches.store'), [
            'city_id' => $city->id,
            'name' => 'Sucursal Nueva',
            'address' => 'Calle Falsa 123',
            'phone' => '5512345678',
            'opening_time' => '09:00',
            'closing_time' => '20:00',
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('branches', ['name' => 'Sucursal Nueva']);
    }

    public function test_closing_time_must_be_after_opening_time(): void
    {
        $city = City::factory()->create();
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.branches.store'), [
            'city_id' => $city->id,
            'name' => 'Sucursal Nueva',
            'address' => 'Calle Falsa 123',
            'phone' => '5512345678',
            'opening_time' => '20:00',
            'closing_time' => '09:00',
        ]);

        $response->assertSessionHasErrors('closing_time');
    }

    public function test_admin_can_update_branch(): void
    {
        $branch = Branch::factory()->create(['name' => 'Original']);
        $this->actingAs($this->admin());

        $response = $this->put(route('admin.branches.update', $branch), [
            'city_id' => $branch->city_id,
            'name' => 'Actualizada',
            'address' => $branch->address,
            'phone' => $branch->phone,
            'opening_time' => '09:00',
            'closing_time' => '20:00',
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('branches', ['id' => $branch->id, 'name' => 'Actualizada']);
    }

    public function test_only_admin_can_delete_branch(): void
    {
        $branch = Branch::factory()->create();
        $this->actingAs($this->manager($branch));

        $response = $this->delete(route('admin.branches.destroy', $branch));

        $response->assertForbidden();
        $this->assertDatabaseHas('branches', ['id' => $branch->id]);
    }

    public function test_admin_can_delete_branch(): void
    {
        $branch = Branch::factory()->create();
        $this->actingAs($this->admin());

        $response = $this->delete(route('admin.branches.destroy', $branch));

        $response->assertRedirect();
        $this->assertDatabaseMissing('branches', ['id' => $branch->id]);
    }
}

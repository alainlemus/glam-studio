<?php

namespace Tests\Feature\Admin;

use App\Models\Branch;
use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class CityControllerTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('admin.cities.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_stylists_cannot_access(): void
    {
        $this->actingAs($this->stylistUser());
        $response = $this->get(route('admin.cities.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_index(): void
    {
        City::factory()->count(3)->create();
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.cities.index'));
        $response->assertOk();
    }

    public function test_admin_can_create_city(): void
    {
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.cities.store'), [
            'name' => 'Mérida',
            'state' => 'Yucatán',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cities', ['name' => 'Mérida']);
    }

    public function test_store_requires_name(): void
    {
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.cities.store'), ['state' => 'Yucatán']);

        $response->assertSessionHasErrors('name');
    }

    public function test_admin_can_update_city(): void
    {
        $city = City::factory()->create(['name' => 'Original']);
        $this->actingAs($this->admin());

        $response = $this->put(route('admin.cities.update', $city), [
            'name' => 'Actualizada',
            'is_active' => false,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cities', ['id' => $city->id, 'name' => 'Actualizada', 'is_active' => false]);
    }

    public function test_admin_can_delete_empty_city(): void
    {
        $city = City::factory()->create();
        $this->actingAs($this->admin());

        $response = $this->delete(route('admin.cities.destroy', $city));

        $response->assertRedirect();
        $this->assertDatabaseMissing('cities', ['id' => $city->id]);
    }

    public function test_cannot_delete_city_with_branches(): void
    {
        $city = City::factory()->create();
        Branch::factory()->create(['city_id' => $city->id]);
        $this->actingAs($this->admin());

        $response = $this->delete(route('admin.cities.destroy', $city));

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('cities', ['id' => $city->id]);
    }
}

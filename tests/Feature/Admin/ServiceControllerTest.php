<?php

namespace Tests\Feature\Admin;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class ServiceControllerTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('admin.services.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_stylists_cannot_access(): void
    {
        $this->actingAs($this->stylistUser());
        $response = $this->get(route('admin.services.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_index(): void
    {
        Service::factory()->count(3)->create();
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.services.index'));
        $response->assertOk();
    }

    public function test_admin_can_create_service(): void
    {
        $category = ServiceCategory::factory()->create();
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.services.store'), [
            'service_category_id' => $category->id,
            'name' => 'Corte Dama',
            'description' => 'Corte y peinado',
            'price' => 350,
            'commission_percentage' => 25,
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('services', [
            'name' => 'Corte Dama',
            'service_category_id' => $category->id,
        ]);
    }

    public function test_store_requires_valid_category(): void
    {
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.services.store'), [
            'service_category_id' => 999,
            'name' => 'Corte Dama',
            'price' => 350,
            'commission_percentage' => 25,
            'duration_minutes' => 60,
        ]);

        $response->assertSessionHasErrors('service_category_id');
    }

    public function test_admin_can_update_service(): void
    {
        $service = Service::factory()->create(['name' => 'Original', 'price' => 100]);
        $this->actingAs($this->admin());

        $response = $this->put(route('admin.services.update', $service), [
            'service_category_id' => $service->service_category_id,
            'name' => 'Actualizado',
            'price' => 200,
            'commission_percentage' => 30,
            'duration_minutes' => 45,
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'name' => 'Actualizado',
            'price' => 200,
        ]);
    }

    public function test_admin_can_delete_service(): void
    {
        $service = Service::factory()->create();
        $this->actingAs($this->admin());

        $response = $this->delete(route('admin.services.destroy', $service));

        $response->assertRedirect();
        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }
}

<?php

namespace Tests\Feature\Admin;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class ServiceCategoryControllerTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('admin.service-categories.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_stylists_cannot_access(): void
    {
        $this->actingAs($this->stylistUser());
        $response = $this->get(route('admin.service-categories.index'));
        $response->assertForbidden();
    }

    public function test_receptionists_cannot_access(): void
    {
        $this->actingAs($this->receptionist());
        $response = $this->get(route('admin.service-categories.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_index(): void
    {
        ServiceCategory::factory()->count(3)->create();
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.service-categories.index'));
        $response->assertOk();
    }

    public function test_manager_can_view_index(): void
    {
        $this->actingAs($this->manager());
        $response = $this->get(route('admin.service-categories.index'));
        $response->assertOk();
    }

    public function test_admin_can_create_category(): void
    {
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.service-categories.store'), [
            'name' => 'Cortes',
            'icon' => '✂️',
            'description' => 'Cortes de cabello',
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('service_categories', [
            'name' => 'Cortes',
            'slug' => 'cortes',
        ]);
    }

    public function test_store_requires_name(): void
    {
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.service-categories.store'), [
            'icon' => '✂️',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('service_categories', 0);
    }

    public function test_admin_can_update_category(): void
    {
        $category = ServiceCategory::factory()->create(['name' => 'Original']);
        $this->actingAs($this->admin());

        $response = $this->put(route('admin.service-categories.update', $category), [
            'name' => 'Actualizada',
            'icon' => '💅',
            'description' => 'Nueva descripción',
            'is_active' => false,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('service_categories', [
            'id' => $category->id,
            'name' => 'Actualizada',
            'is_active' => false,
        ]);
    }

    public function test_admin_can_delete_empty_category(): void
    {
        $category = ServiceCategory::factory()->create();
        $this->actingAs($this->admin());

        $response = $this->delete(route('admin.service-categories.destroy', $category));

        $response->assertRedirect();
        $this->assertDatabaseMissing('service_categories', ['id' => $category->id]);
    }

    public function test_cannot_delete_category_with_services(): void
    {
        $category = ServiceCategory::factory()->create();
        Service::factory()->create(['service_category_id' => $category->id]);
        $this->actingAs($this->admin());

        $response = $this->delete(route('admin.service-categories.destroy', $category));

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('service_categories', ['id' => $category->id]);
    }
}

<?php

namespace Tests\Feature\Admin;

use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class TestimonialControllerTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('admin.testimonials.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_stylists_cannot_access(): void
    {
        $this->actingAs($this->stylistUser());
        $response = $this->get(route('admin.testimonials.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_index(): void
    {
        Testimonial::factory()->count(3)->create();
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.testimonials.index'));
        $response->assertOk();
    }

    public function test_admin_can_create_testimonial(): void
    {
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.testimonials.store'), [
            'client_name' => 'María López',
            'quote' => 'Excelente servicio, muy recomendado.',
            'rating' => 5,
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('testimonials', ['client_name' => 'María López']);
    }

    public function test_store_requires_rating_between_1_and_5(): void
    {
        $this->actingAs($this->admin());

        $response = $this->post(route('admin.testimonials.store'), [
            'client_name' => 'María López',
            'quote' => 'Excelente servicio.',
            'rating' => 6,
        ]);

        $response->assertSessionHasErrors('rating');
    }

    public function test_admin_can_update_testimonial(): void
    {
        $testimonial = Testimonial::factory()->create(['client_name' => 'Original']);
        $this->actingAs($this->admin());

        $response = $this->put(route('admin.testimonials.update', $testimonial), [
            'client_name' => 'Actualizado',
            'quote' => $testimonial->quote,
            'rating' => 4,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('testimonials', ['id' => $testimonial->id, 'client_name' => 'Actualizado', 'rating' => 4]);
    }

    public function test_admin_can_delete_testimonial(): void
    {
        $testimonial = Testimonial::factory()->create();
        $this->actingAs($this->admin());

        $response = $this->delete(route('admin.testimonials.destroy', $testimonial));

        $response->assertRedirect();
        $this->assertDatabaseMissing('testimonials', ['id' => $testimonial->id]);
    }
}

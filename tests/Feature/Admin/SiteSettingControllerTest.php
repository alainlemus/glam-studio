<?php

namespace Tests\Feature\Admin;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class SiteSettingControllerTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('admin.settings.edit'));
        $response->assertRedirect(route('login'));
    }

    public function test_managers_cannot_access(): void
    {
        $this->actingAs($this->manager());
        $response = $this->get(route('admin.settings.edit'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_settings(): void
    {
        $this->actingAs($this->admin());
        $response = $this->get(route('admin.settings.edit'));
        $response->assertOk();
    }

    public function test_admin_can_update_settings(): void
    {
        $this->actingAs($this->admin());

        $response = $this->put(route('admin.settings.update'), [
            'site_name' => 'Nuevo Nombre',
            'tagline' => 'Nuevo Tagline',
            'notification_email' => 'reservas@example.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('site_settings', ['site_name' => 'Nuevo Nombre', 'tagline' => 'Nuevo Tagline']);
    }

    public function test_update_requires_valid_notification_email(): void
    {
        $this->actingAs($this->admin());

        $response = $this->put(route('admin.settings.update'), [
            'site_name' => 'Glam Studio',
            'notification_email' => 'no-es-un-correo',
        ]);

        $response->assertSessionHasErrors('notification_email');
    }

    public function test_admin_can_upload_a_logo(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());

        $response = $this->put(route('admin.settings.update'), [
            'site_name' => 'Glam Studio',
            'logo' => UploadedFile::fake()->image('logo.png', 200, 200),
        ]);

        $response->assertRedirect();
        $settings = SiteSetting::current();
        $this->assertNotNull($settings->logo_path);
        Storage::disk('public')->assertExists($settings->logo_path);
        $this->assertStringContainsString($settings->logo_path, $settings->logo_url);

        // logo_url debe viajar al frontend (requiere estar en $appends); sin esto
        // el logo subido nunca se vería en el sitio público ni en el admin.
        $editResponse = $this->get(route('admin.settings.edit'));
        $editResponse->assertInertia(fn ($page) => $page->where('settings.logo_url', $settings->logo_url));
    }

    public function test_admin_can_remove_the_logo(): void
    {
        Storage::fake('public');
        $settings = SiteSetting::current();
        $settings->update(['logo_path' => 'branding/existing.png']);
        Storage::disk('public')->put('branding/existing.png', 'fake-image-content');
        $this->actingAs($this->admin());

        $response = $this->put(route('admin.settings.update'), [
            'site_name' => 'Glam Studio',
            'remove_logo' => true,
        ]);

        $response->assertRedirect();
        Storage::disk('public')->assertMissing('branding/existing.png');
        $this->assertNull(SiteSetting::current()->logo_path);
    }
}

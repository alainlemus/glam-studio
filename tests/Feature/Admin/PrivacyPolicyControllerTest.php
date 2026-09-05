<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class PrivacyPolicyControllerTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('admin.privacy-policy.edit'));
        $response->assertRedirect(route('login'));
    }

    public function test_managers_cannot_access(): void
    {
        $this->actingAs($this->manager());
        $response = $this->get(route('admin.privacy-policy.edit'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_privacy_policy(): void
    {
        $this->actingAs($this->admin());
        $response = $this->get(route('admin.privacy-policy.edit'));
        $response->assertOk();
    }

    public function test_admin_can_update_privacy_policy(): void
    {
        $this->actingAs($this->admin());

        $response = $this->put(route('admin.privacy-policy.update'), [
            'privacy_policy' => '<p>Nueva política de privacidad</p>',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('site_settings', ['privacy_policy' => '<p>Nueva política de privacidad</p>']);
    }

    public function test_update_requires_privacy_policy_content(): void
    {
        $this->actingAs($this->admin());

        $response = $this->put(route('admin.privacy-policy.update'), [
            'privacy_policy' => '',
        ]);

        $response->assertSessionHasErrors('privacy_policy');
    }
}

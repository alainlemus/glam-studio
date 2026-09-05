<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Sale;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('admin.audit-log.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_managers_cannot_access(): void
    {
        $this->actingAs($this->manager());
        $response = $this->get(route('admin.audit-log.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_index(): void
    {
        AuditLog::factory()->count(3)->create();
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.audit-log.index'));
        $response->assertOk();
    }

    public function test_changing_service_price_is_logged(): void
    {
        $service = Service::factory()->create(['price' => 100]);
        $this->actingAs($this->admin());

        $this->put(route('admin.services.update', $service), [
            'service_category_id' => $service->service_category_id,
            'name' => $service->name,
            'price' => 200,
            'commission_percentage' => 25,
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'price_changed',
            'auditable_type' => Service::class,
            'auditable_id' => $service->id,
        ]);
    }

    public function test_unchanged_price_is_not_logged(): void
    {
        $service = Service::factory()->create(['price' => 100]);
        $this->actingAs($this->admin());

        $this->put(route('admin.services.update', $service), [
            'service_category_id' => $service->service_category_id,
            'name' => $service->name,
            'price' => 100,
            'commission_percentage' => 25,
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $this->assertDatabaseCount('audit_logs', 0);
    }

    public function test_deleting_a_client_is_logged(): void
    {
        $client = Client::factory()->create();
        $this->actingAs($this->admin());

        $this->delete(route('admin.clients.destroy', $client));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'deleted',
            'auditable_type' => Client::class,
            'auditable_id' => $client->id,
        ]);
    }

    public function test_blocking_a_client_is_logged(): void
    {
        $client = Client::factory()->create(['is_blocked' => false]);
        $this->actingAs($this->admin());

        $this->put(route('admin.clients.update', $client), [
            'name' => $client->name,
            'phone' => $client->phone,
            'is_active' => true,
            'is_blocked' => true,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'status_changed',
            'auditable_type' => Client::class,
            'auditable_id' => $client->id,
        ]);
    }

    public function test_changing_user_role_is_logged(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $this->actingAs($this->admin());

        $this->put(route('admin.users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'role_changed',
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
        ]);
    }

    public function test_deleting_a_user_is_logged(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $this->actingAs($this->admin());

        $this->delete(route('admin.users.destroy', $user));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'deleted',
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
        ]);
    }

    public function test_cancelling_a_sale_is_logged(): void
    {
        $sale = Sale::factory()->create(['status' => 'paid']);
        $this->actingAs($this->admin());

        $this->delete(route('admin.sales.destroy', $sale));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'cancelled',
            'auditable_type' => Sale::class,
            'auditable_id' => $sale->id,
        ]);
    }

    public function test_deleting_a_branch_is_logged(): void
    {
        $branch = Branch::factory()->create();
        $this->actingAs($this->admin());

        $this->delete(route('admin.branches.destroy', $branch));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'deleted',
            'auditable_type' => Branch::class,
            'auditable_id' => $branch->id,
        ]);
    }

    public function test_filters_by_action(): void
    {
        AuditLog::factory()->create(['action' => 'deleted']);
        AuditLog::factory()->create(['action' => 'price_changed']);
        $this->actingAs($this->admin());

        $response = $this->get(route('admin.audit-log.index', ['action' => 'deleted']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->has('logs.data', 1));
    }
}

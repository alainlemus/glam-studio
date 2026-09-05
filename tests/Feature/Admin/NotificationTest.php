<?php

namespace Tests\Feature\Admin;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Service;
use App\Notifications\AppointmentCancelledNotification;
use App\Notifications\LowStockNotification;
use App\Notifications\NewAppointmentNotification;
use App\Notifications\NoShowNotification;
use App\Support\NotificationRecipients;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    public function test_recipients_for_branch_includes_admins_and_branch_staff_only(): void
    {
        $branch = Branch::factory()->create();
        $otherBranch = Branch::factory()->create();

        $admin = $this->admin();
        $ownManager = $this->manager($branch);
        $ownReceptionist = $this->receptionist($branch);
        $otherManager = $this->manager($otherBranch);
        $stylist = $this->stylistUser($branch);

        $recipients = NotificationRecipients::forBranch($branch->id)->pluck('id');

        $this->assertTrue($recipients->contains($admin->id));
        $this->assertTrue($recipients->contains($ownManager->id));
        $this->assertTrue($recipients->contains($ownReceptionist->id));
        $this->assertFalse($recipients->contains($otherManager->id));
        $this->assertFalse($recipients->contains($stylist->id));
    }

    public function test_booking_a_new_appointment_notifies_branch_staff(): void
    {
        Notification::fake();

        $branch = Branch::factory()->create();
        $service = Service::factory()->create(['price' => 300, 'duration_minutes' => 30]);
        $client = Client::factory()->create();
        $admin = $this->admin();

        $this->post('/agendar', [
            'name' => $client->name,
            'phone' => $client->phone,
            'email' => 'nuevo@example.com',
            'branch_id' => $branch->id,
            'service_id' => $service->id,
            'date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '10:00',
        ]);

        Notification::assertSentTo(
            NotificationRecipients::forBranch($branch->id),
            NewAppointmentNotification::class,
        );
    }

    public function test_admin_can_view_notification_bell_data(): void
    {
        $admin = $this->admin();
        $appointment = Appointment::factory()->create();
        $admin->notify(new NewAppointmentNotification($appointment->fresh()->load('client', 'branch', 'services.service')));

        $this->actingAs($admin);
        $response = $this->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('notifications.unreadCount', 1)
            ->has('notifications.recent', 1)
        );
    }

    public function test_admin_can_mark_notification_as_read(): void
    {
        $admin = $this->admin();
        $appointment = Appointment::factory()->create();
        $admin->notify(new NewAppointmentNotification($appointment->fresh()->load('client', 'branch', 'services.service')));
        $notificationId = $admin->notifications()->first()->id;

        $this->actingAs($admin);
        $response = $this->post(route('admin.notifications.read', $notificationId));

        $response->assertRedirect();
        $this->assertNotNull($admin->notifications()->find($notificationId)->read_at);
    }

    public function test_user_cannot_mark_another_users_notification_as_read(): void
    {
        $admin = $this->admin();
        $otherAdmin = $this->admin();
        $appointment = Appointment::factory()->create();
        $otherAdmin->notify(new NewAppointmentNotification($appointment->fresh()->load('client', 'branch', 'services.service')));
        $notificationId = $otherAdmin->notifications()->first()->id;

        $this->actingAs($admin);
        $response = $this->post(route('admin.notifications.read', $notificationId));

        $response->assertNotFound();
        $this->assertNull($otherAdmin->notifications()->find($notificationId)->read_at);
    }

    public function test_admin_can_mark_all_notifications_as_read(): void
    {
        $admin = $this->admin();
        $appointment = Appointment::factory()->create();
        $admin->notify(new NewAppointmentNotification($appointment->fresh()->load('client', 'branch', 'services.service')));
        $admin->notify(new NoShowNotification($appointment));

        $this->actingAs($admin);
        $response = $this->post(route('admin.notifications.read_all'));

        $response->assertRedirect();
        $this->assertEquals(0, $admin->unreadNotifications()->count());
    }

    public function test_cancelling_appointment_notifies_branch_staff(): void
    {
        Notification::fake();

        $branch = Branch::factory()->create();
        $appointment = Appointment::factory()->create(['branch_id' => $branch->id, 'status' => 'pending']);
        $this->actingAs($this->admin());

        $this->post(route('admin.appointments.cancel', $appointment));

        Notification::assertSentTo(
            NotificationRecipients::forBranch($branch->id),
            AppointmentCancelledNotification::class,
        );
    }

    public function test_no_show_notifies_branch_staff(): void
    {
        Notification::fake();

        $branch = Branch::factory()->create();
        $appointment = Appointment::factory()->create(['branch_id' => $branch->id, 'status' => 'confirmed']);
        $this->actingAs($this->admin());

        $this->post(route('admin.appointments.no_show', $appointment));

        Notification::assertSentTo(
            NotificationRecipients::forBranch($branch->id),
            NoShowNotification::class,
        );
    }

    public function test_stock_adjustment_crossing_threshold_notifies_branch_staff(): void
    {
        Notification::fake();

        $branch = Branch::factory()->create();
        $product = Product::factory()->create(['min_stock' => 5]);
        ProductStock::factory()->create(['product_id' => $product->id, 'branch_id' => $branch->id, 'stock' => 10, 'min_stock' => 5]);
        $this->actingAs($this->admin());

        $this->post(route('admin.inventory.adjust', [$product, $branch]), ['adjustment' => -7]);

        Notification::assertSentTo(
            NotificationRecipients::forBranch($branch->id),
            LowStockNotification::class,
        );
    }

    public function test_stock_adjustment_already_low_does_not_renotify(): void
    {
        Notification::fake();

        $branch = Branch::factory()->create();
        $product = Product::factory()->create(['min_stock' => 5]);
        ProductStock::factory()->create(['product_id' => $product->id, 'branch_id' => $branch->id, 'stock' => 3, 'min_stock' => 5]);
        $this->actingAs($this->admin());

        $this->post(route('admin.inventory.adjust', [$product, $branch]), ['adjustment' => -1]);

        Notification::assertNothingSent();
    }
}

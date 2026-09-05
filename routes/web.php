<?php

use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\CommissionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExpenseCategoryController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\IncomeController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\LoyaltyController;
use App\Http\Controllers\Admin\MarketingController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PrivacyPolicyController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\StylistController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\PublicAppointmentController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\WhatsAppWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Public site
Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/sucursales', [SiteController::class, 'branches'])->name('site.branches');
Route::get('/sucursales/{slug}', [SiteController::class, 'showBranch'])->name('site.branch.show');
Route::get('/servicios', [SiteController::class, 'services'])->name('site.services');
Route::get('/productos', [SiteController::class, 'products'])->name('site.products');
Route::get('/nosotros', [SiteController::class, 'about'])->name('site.about');
Route::get('/promociones', [SiteController::class, 'promotions'])->name('site.promotions');
Route::get('/contacto', [SiteController::class, 'contact'])->name('site.contact');
Route::get('/aviso-de-privacidad', [SiteController::class, 'privacyPolicy'])->name('site.privacy');

// Booking
Route::get('/agendar', [PublicAppointmentController::class, 'create'])->name('site.book');
Route::post('/agendar', [PublicAppointmentController::class, 'store'])->name('site.book.store');
Route::post('/api/slots', [PublicAppointmentController::class, 'availableSlots'])->name('api.slots');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        if (auth()->user()->isAdmin() || auth()->user()->isManager()) {
            return redirect()->route('admin.dashboard');
        }
        if (auth()->user()->isReceptionist()) {
            return redirect()->route('admin.appointments.calendar');
        }

        // No hay panel dedicado para estilistas todavía; se les envía a su perfil
        // en lugar de a una ruta inexistente para evitar un error 500.
        return redirect()->route('profile.edit');
    })->name('dashboard');
});

// WhatsApp Webhook
Route::get('/webhook/whatsapp', [WhatsAppWebhookController::class, 'verify'])->name('webhook.whatsapp.verify');
Route::post('/webhook/whatsapp', [WhatsAppWebhookController::class, 'webhook'])->name('webhook.whatsapp');

// Admin Panel
Route::middleware(['auth', 'verified', 'role:admin,manager,receptionist,stylist'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read_all');

        // Citas, agenda, marketing y clientes: admin, gerente y recepcionista
        // (todo se filtra por sucursal para gerente/recepcionista, ver branchScope()).
        Route::middleware('role:admin,manager,receptionist')->group(function () {
            Route::get('appointments/calendar', [AppointmentController::class, 'calendar'])->name('appointments.calendar');
            Route::resource('appointments', AppointmentController::class);
            Route::post('appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
            Route::post('appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
            Route::post('appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');
            Route::post('appointments/{appointment}/no-show', [AppointmentController::class, 'noShow'])->name('appointments.no_show');

            Route::resource('clients', ClientController::class);

            Route::resource('marketing', MarketingController::class)
                ->except(['show'])
                ->parameters(['marketing' => 'campaign']);
            Route::post('marketing/{campaign}/activate', [MarketingController::class, 'activate'])->name('marketing.activate');
            Route::post('marketing/{campaign}/send', [MarketingController::class, 'send'])->name('marketing.send');
        });

        // Resto de la operación: solo admin y gerente (gerente restringido a su sucursal)
        Route::middleware('role:admin,manager')->group(function () {
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

            // Sucursales y Ciudades
            Route::resource('branches', BranchController::class);
            Route::resource('cities', CityController::class)->except(['show', 'edit']);
            Route::resource('expense-categories', ExpenseCategoryController::class)
                ->except(['show', 'create', 'edit'])
                ->parameters(['expense-categories' => 'category']);

            // Servicios
            Route::resource('services', ServiceController::class)->except(['show']);
            Route::resource('service-categories', ServiceCategoryController::class)
                ->except(['show', 'create', 'edit'])
                ->parameters(['service-categories' => 'category']);

            // Productos e Inventario
            Route::resource('products', ProductController::class)->except(['show']);
            Route::resource('product-categories', ProductCategoryController::class)
                ->except(['show', 'create', 'edit'])
                ->parameters(['product-categories' => 'category']);
            Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
            Route::post('inventory/{product}/{branch}/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
            Route::post('inventory/transfer', [InventoryController::class, 'transfer'])->name('inventory.transfer');

            // Estilistas
            Route::resource('stylists', StylistController::class);

            // Ventas
            Route::get('sales/export', [SaleController::class, 'export'])->name('sales.export');
            Route::resource('sales', SaleController::class)->except(['edit', 'update']);
            Route::get('sales/{sale}/ticket', [SaleController::class, 'ticket'])->name('sales.ticket');

            // Comisiones
            Route::get('commissions', [CommissionController::class, 'index'])->name('commissions.index');
            Route::get('commissions/export', [CommissionController::class, 'export'])->name('commissions.export');
            Route::post('commissions/{commission}/pay', [CommissionController::class, 'pay'])->name('commissions.pay');
            Route::post('commissions/pay-batch', [CommissionController::class, 'payBatch'])->name('commissions.pay_batch');

            // Finanzas
            Route::get('finance', [FinanceController::class, 'index'])->name('finance.index');
            Route::get('expenses/export', [ExpenseController::class, 'export'])->name('expenses.export');
            Route::resource('expenses', ExpenseController::class)->except(['show']);
            Route::get('incomes/export', [IncomeController::class, 'export'])->name('incomes.export');
            Route::resource('incomes', IncomeController::class)->only(['index', 'destroy']);

            // Lealtad
            Route::get('loyalty', [LoyaltyController::class, 'index'])->name('loyalty.index');
            Route::post('loyalty/{card}/add-stamp', [LoyaltyController::class, 'addStamp'])->name('loyalty.add_stamp');
            Route::post('loyalty/{card}/redeem', [LoyaltyController::class, 'redeem'])->name('loyalty.redeem');

            // Testimonios
            Route::resource('testimonials', TestimonialController::class)->except(['show']);
        });

        // Usuarios, configuración del sitio y aviso de privacidad: solo administradores
        Route::middleware('role:admin')->group(function () {
            Route::resource('users', UserController::class)->except(['show']);

            Route::get('settings', [SiteSettingController::class, 'edit'])->name('settings.edit');
            Route::put('settings', [SiteSettingController::class, 'update'])->name('settings.update');

            Route::get('privacy-policy', [PrivacyPolicyController::class, 'edit'])->name('privacy-policy.edit');
            Route::put('privacy-policy', [PrivacyPolicyController::class, 'update'])->name('privacy-policy.update');

            Route::get('audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');
        });
    });

require __DIR__.'/settings.php';

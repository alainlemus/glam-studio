<?php

use App\Http\Controllers\Admin\AppointmentController;
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
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\StylistController;
use App\Http\Controllers\PublicAppointmentController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

// Public site
Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/sucursales', [SiteController::class, 'branches'])->name('site.branches');
Route::get('/sucursales/{slug}', [SiteController::class, 'showBranch'])->name('site.branch.show');
Route::get('/servicios', [SiteController::class, 'services'])->name('site.services');
Route::get('/nosotros', [SiteController::class, 'about'])->name('site.about');
Route::get('/promociones', [SiteController::class, 'promotions'])->name('site.promotions');
Route::get('/contacto', [SiteController::class, 'contact'])->name('site.contact');

// Booking
Route::get('/agendar', [PublicAppointmentController::class, 'create'])->name('site.book');
Route::post('/agendar', [PublicAppointmentController::class, 'store'])->name('site.book.store');
Route::post('/api/slots', [PublicAppointmentController::class, 'availableSlots'])->name('api.slots');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        if (auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->isReceptionist()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('admin.stylist.dashboard');
    })->name('dashboard');
});

// WhatsApp Webhook
Route::get('/webhook/whatsapp', [\App\Http\Controllers\WhatsAppWebhookController::class, 'verify'])->name('webhook.whatsapp.verify');
Route::post('/webhook/whatsapp', [\App\Http\Controllers\WhatsAppWebhookController::class, 'webhook'])->name('webhook.whatsapp');

// Admin Panel
Route::middleware(['auth', 'verified', 'role:admin,manager,receptionist,stylist'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Sucursales y Ciudades
        Route::resource('branches', BranchController::class);
        Route::resource('cities', CityController::class);
        Route::resource('expense-categories', ExpenseCategoryController::class)->except(['show']);

        // Servicios
        Route::resource('services', ServiceController::class);
        Route::resource('service-categories', ServiceCategoryController::class);

        // Productos e Inventario
        Route::resource('products', ProductController::class);
        Route::resource('product-categories', ProductCategoryController::class);
        Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::post('inventory/{product}/{branch}/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
        Route::post('inventory/transfer', [InventoryController::class, 'transfer'])->name('inventory.transfer');

        // Estilistas y Clientes
        Route::resource('stylists', StylistController::class);
        Route::resource('clients', ClientController::class);

        // Citas
        Route::get('appointments/calendar', [AppointmentController::class, 'calendar'])->name('appointments.calendar');
        Route::resource('appointments', AppointmentController::class);
        Route::post('appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
        Route::post('appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
        Route::post('appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');
        Route::post('appointments/{appointment}/no-show', [AppointmentController::class, 'noShow'])->name('appointments.no_show');

        // Ventas
        Route::resource('sales', SaleController::class);
        Route::get('sales/{sale}/ticket', [SaleController::class, 'ticket'])->name('sales.ticket');

        // Comisiones
        Route::get('commissions', [CommissionController::class, 'index'])->name('commissions.index');
        Route::post('commissions/{commission}/pay', [CommissionController::class, 'pay'])->name('commissions.pay');
        Route::post('commissions/pay-batch', [CommissionController::class, 'payBatch'])->name('commissions.pay_batch');

        // Finanzas
        Route::get('finance', [FinanceController::class, 'index'])->name('finance.index');
        Route::resource('expenses', ExpenseController::class);
        Route::resource('incomes', IncomeController::class);

        // Marketing
        Route::resource('marketing', MarketingController::class);
        Route::post('marketing/{campaign}/activate', [MarketingController::class, 'activate'])->name('marketing.activate');
        Route::post('marketing/{campaign}/send', [MarketingController::class, 'send'])->name('marketing.send');

        // Lealtad
        Route::get('loyalty', [LoyaltyController::class, 'index'])->name('loyalty.index');
        Route::post('loyalty/{card}/add-stamp', [LoyaltyController::class, 'addStamp'])->name('loyalty.add_stamp');
        Route::post('loyalty/{card}/redeem', [LoyaltyController::class, 'redeem'])->name('loyalty.redeem');
    });

require __DIR__.'/settings.php';
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\RestaurantTableController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StaffSalaryAdvanceController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    try {
        return redirect()->route('login');
    } catch (\Exception $e) {
        \Log::error('Error in root route: ' . $e->getMessage());
        return response('Error: ' . $e->getMessage(), 500);
    }
});

// Test route to verify server is working
Route::get('/test', function () {
    return response('Server is working!', 200);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/monthly-revenue', [DashboardController::class, 'getMonthlyRevenueData'])->name('dashboard.monthly-revenue');
    Route::get('/dashboard/peak-hours/overall', [DashboardController::class, 'getOverallPeakHours'])->name('dashboard.peak-hours.overall');
    Route::get('/dashboard/peak-hours/date/{date}', [DashboardController::class, 'getPeakHoursByDate'])->name('dashboard.peak-hours.date');
    
    // POS - Point of Sale
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::post('/pos', [POSController::class, 'store'])->name('pos.store');
    
    // Tables
    Route::resource('tables', RestaurantTableController::class);
    
    // Orders
    Route::resource('orders', OrderController::class);
    Route::post('/order-items/{orderItem}/update-status', [OrderController::class, 'updateStatus'])->name('order-items.update-status');
    
    // Bills
    Route::resource('bills', BillController::class);
    Route::get('bills/{bill}/print', [BillController::class, 'print'])->name('bills.print');
    Route::get('bills/{bill}/preview', [BillController::class, 'preview'])->name('bills.preview');
    Route::get('bills/{bill}/download', [BillController::class, 'download'])->name('bills.download');
    
    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::put('/settings/app', [SettingsController::class, 'updateSettings'])->name('settings.app.update')->middleware('role:admin');
    
    // User Management (Admin only)
    Route::middleware('role:admin')->prefix('settings/users')->name('settings.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });
    
    // Staff Salary Advances
    Route::middleware(['auth', 'role:admin|manager'])->group(function () {
        Route::get('/staff/salary-advances', [StaffSalaryAdvanceController::class, 'index'])->name('staff-salary-advances.index');
        Route::get('/staff/salary-advances/{advance}', [StaffSalaryAdvanceController::class, 'show'])->name('staff-salary-advances.show');
        Route::get('/staff/salary-advances/{advance}/edit', [StaffSalaryAdvanceController::class, 'edit'])->name('staff-salary-advances.edit');
        Route::put('/staff/salary-advances/{advance}', [StaffSalaryAdvanceController::class, 'update'])->name('staff-salary-advances.update');
        Route::post('/staff/salary-advances', [StaffSalaryAdvanceController::class, 'store'])->name('staff-salary-advances.store');
        Route::get('/api/staff/salary-advances/summary', [StaffSalaryAdvanceController::class, 'summary'])->name('api.staff-salary-advances.summary');
    });
    
    // Admin only routes
    Route::middleware('role:admin')->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('menu-items', MenuItemController::class);
        
        // Staff Management
        Route::get('/staff', [\App\Http\Controllers\StaffController::class, 'index'])->name('staff.index');
        Route::post('/staff', [\App\Http\Controllers\StaffController::class, 'store'])->name('staff.store');
        Route::delete('/staff/{staff}', [\App\Http\Controllers\StaffController::class, 'destroy'])->name('staff.destroy');
    });
});

require __DIR__.'/auth.php';

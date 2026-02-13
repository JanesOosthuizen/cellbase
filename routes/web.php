<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\ImeiController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\ExternalUserController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\RepairSettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\LoanDeviceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    
    Route::get('/settings', function () {
        return view('settings');
    })->name('settings');

    Route::resource('users', UserController::class)->except(['show']);
    Route::get('/settings/repairs', [RepairSettingsController::class, 'edit'])->name('settings.repairs.edit');
    Route::put('/settings/repairs', [RepairSettingsController::class, 'update'])->name('settings.repairs.update');
    
    Route::resource('manufacturers', ManufacturerController::class);
    Route::resource('external-users', ExternalUserController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('repairs', RepairController::class);
    Route::patch('repairs/{repair}/status', [RepairController::class, 'updateStatus'])->name('repairs.update-status');

    Route::resource('loan-devices', LoanDeviceController::class);
    
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
    Route::get('/devices/create', [DeviceController::class, 'create'])->name('devices.create');
    Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');
    Route::get('/devices/import', [DeviceController::class, 'importForm'])->name('devices.import');
    Route::post('/devices/import', [DeviceController::class, 'importExcel'])->name('devices.import.excel');
    
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::post('/invoices/{invoice}/approve', [InvoiceController::class, 'approve'])->name('invoices.approve');
    
    Route::get('/imeis', [ImeiController::class, 'index'])->name('imeis.index');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::get('/api/orders/search-devices', [OrderController::class, 'searchDevices'])->name('api.orders.search-devices');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    
    // API Routes for AJAX
    Route::post('/api/check-imei', [ImeiController::class, 'checkImei'])->name('api.check-imei');
    Route::get('/api/global-search', GlobalSearchController::class)->name('api.global-search');
});

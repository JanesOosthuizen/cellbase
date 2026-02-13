<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\LoanDeviceController;
use App\Http\Controllers\Api\RepairController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public auth (for React Native app)
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (send header: Authorization: Bearer <token>)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/repairs', [RepairController::class, 'index']);
    Route::get('/repairs/{repair}', [RepairController::class, 'show']);
    Route::post('/repairs', [RepairController::class, 'store']);
    Route::put('/repairs/{repair}', [RepairController::class, 'update']);
    Route::patch('/repairs/{repair}', [RepairController::class, 'update']);

    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/{customer}', [CustomerController::class, 'show']);
    Route::get('/devices', [DeviceController::class, 'index']);

    Route::get('/loan-devices', [LoanDeviceController::class, 'index']);
    Route::post('/loan-devices/{loanDevice}/allocate', [LoanDeviceController::class, 'allocate']);
});

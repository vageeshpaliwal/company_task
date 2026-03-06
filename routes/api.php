<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\AllocatipnController as AllocationController;
use App\Http\Controllers\Api\UserController;



Route::post('/login', [AuthController::class, 'login']);
Route::post('/signup', [AuthController::class, 'signup']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('devices', DeviceController::class);
    Route::get('users', [UserController::class, 'index']);
    Route::post('allocate-device', [AllocationController::class, 'store']);
    Route::apiResource('tickets', TicketController::class);
    Route::patch('/tickets/{id}/status', [TicketController::class, 'updateStatus']);
    Route::delete('deallocate-device', [AllocationController::class, 'deallocate']);
});

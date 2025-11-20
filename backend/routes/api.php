<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\TableController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\KitchenOrderTicketController;
use App\Http\Controllers\Api\BillController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Menu
    Route::get('/menu', [MenuController::class, 'index']);
    Route::get('/menu/{menuItem}', [MenuController::class, 'show']);
    Route::get('/categories', [MenuController::class, 'categories']);
    
    // Tables
    Route::get('/tables', [TableController::class, 'index']);
    Route::get('/tables/{table}', [TableController::class, 'show']);
    Route::put('/tables/{table}/status', [TableController::class, 'updateStatus']);
    Route::post('/tables/recalculate-statuses', [TableController::class, 'recalculateStatuses']);
    
    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::put('/orders/{order}', [OrderController::class, 'update']);
    Route::post('/orders/{order}/items', [OrderController::class, 'addItems']);
    Route::post('/orders/{order}/complete', [OrderController::class, 'complete']);
    Route::put('/orders/{order}/items/{orderItem}/status', [OrderController::class, 'updateItemStatus']);
    Route::delete('/orders/{order}', [OrderController::class, 'destroy']);
    
    // Kitchen Order Tickets (KOT)
    Route::get('/orders/{order}/kots', [KitchenOrderTicketController::class, 'index']);
    Route::get('/kots/{kot}', [KitchenOrderTicketController::class, 'show']);
    Route::post('/kots/{kot}/print', [KitchenOrderTicketController::class, 'print']);
    Route::put('/kots/{kot}/status', [KitchenOrderTicketController::class, 'updateStatus']);
    Route::get('/kots/pending/all', [KitchenOrderTicketController::class, 'pending']);
    
    // Bills
    Route::get('/bills', [BillController::class, 'index']);
    Route::post('/orders/{order}/bill', [BillController::class, 'store']);
    Route::get('/bills/{bill}', [BillController::class, 'show']);
    Route::post('/bills/{bill}/pay', [BillController::class, 'pay']);
});

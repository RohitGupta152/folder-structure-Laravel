<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Student\Controllers\StudentController;
use App\Modules\Order\Controllers\OrderController;
use App\Modules\Auth\Controllers\AuthController;


// Route::get('/orders', [OrderController::class, 'index']);


Route::prefix('students')->group(function () {
    Route::post('/create', [StudentController::class, 'createStudent']);
    Route::post('/get-student', [StudentController::class, 'getStudent']);
    Route::post('/export-student', [StudentController::class, 'exportStudent']);
    Route::post('/update', [StudentController::class, 'updateStudent']);
    Route::post('/delete', [StudentController::class, 'deleteStudent']);
});


Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'registerUser']);
    Route::post('/admin-register', [AuthController::class, 'AdminRegister']);
    Route::post('/login', [AuthController::class, 'loginUser']);

    Route::middleware(['auth:sanctum'])->group(function () {

        Route::controller(AuthController::class)->group(function () {
            Route::prefix('user')->group(function () {
                Route::post('/get', 'getUser');
                Route::post('/update', 'updateUser');
                Route::post('/logout', 'logoutUser');
            });
        });

        Route::prefix('order')->controller(OrderController::class)->group(function () {
            Route::post('/create-order', 'createOrder');

            Route::post('/get-order', 'getOrders'); // (Filters) (All in one)
            Route::post('/export-order', 'exportOrders');
            Route::post('/active-order', 'getActiveOrders');

            Route::post('/update', 'updateOrder');
            Route::post('/update-product', 'updateOrderProductDetails');
            Route::post('/delete-order', 'deleteOrder');
            Route::post('/cancel', 'updateCancelOrder');
        });
    });

    Route::middleware(['auth:sanctum', 'admin_or_sub-admin'])->group(function () {
        Route::prefix('order')->controller(OrderController::class)->group(function () {
            Route::post('/order-status', 'updateOrderStatus');
        });
    });
});

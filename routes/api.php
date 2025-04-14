<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Student\Controllers\StudentController;
use App\Modules\Order\Controllers\OrderController;
use App\Modules\Auth\Controllers\AuthController;
use App\Modules\Auth\Controllers\UserController;
use App\modules\Rate_chart\Controllers\RateChartController;


// ->middleware(['auth:sanctum'])
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



Route::middleware(['auth:sanctum', 'admin_or_sub-admin'])
    ->controller(RateChartController::class)
    ->prefix('v1/rate')
    ->group(function () {
        Route::post('/create', 'createRate');

        Route::post('/get-rates', 'getRates');
        Route::post('/export-rates', 'exportRates');

        Route::post('/update', 'updateRate');
        Route::post('/delete', 'deleteRate');
    });



// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes with Sanctum Authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::controller(AuthController::class)->group(function () {
        Route::post('/admin-sub-admin/dashboard', 'adminSubAdminDashboard');
        Route::post('/admin/dashboard', 'adminDashboard');
        Route::post('/sub-admin/dashboard', 'subAdminDashboard');
        Route::post('/user/dashboard', 'userDashboard');
    });

    Route::controller(UserController::class)->group(function () {
        Route::post('/admin/users/create', 'createUser');
        Route::post('/admin/users/list', 'getUserList');
        Route::post('/admin/users/view', 'getUserById');
        Route::post('/admin/users/update', 'updateUserById');
        Route::post('/admin/users/update-role', 'updateUserByIdAndRole');
        Route::post('/admin/users/delete', 'deleteUserById');
    });
});

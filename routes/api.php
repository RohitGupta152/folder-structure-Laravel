<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Student\Controllers\StudentController;
use App\Modules\Order\Controllers\OrderController;

Route::get('/orders', [OrderController::class, 'index']);



Route::prefix('students')->group(function () {
    Route::post('/create', [StudentController::class, 'createStudent']);
    Route::post('/get-student', [StudentController::class, 'getStudent']);
    Route::post('/export-student', [StudentController::class, 'exportStudent']);
    Route::post('/update', [StudentController::class, 'updateStudent']);
    Route::post('/delete', [StudentController::class, 'deleteStudent']);
});

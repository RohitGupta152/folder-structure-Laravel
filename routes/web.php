<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

// Route::get('/cache-test', function () {
//     $cacheKey = 'test_data';
//     if (Cache::has($cacheKey)) {
//         $data = Cache::get($cacheKey);
//         return response()->json([
//             'source' => 'CACHE',
//             'data' => $data,
//         ]);
//     }
//     $data = [
//         'id' => 1,
//         'name' => 'John Doe',
//         'email' => 'john@example.com',
//         'created_at' => now()->toDateTimeString(),
//     ];
//     Cache::put($cacheKey, $data, now()->addMinutes(5));
//     return response()->json([
//         'source' => 'NEW DATA (stored in cache)',
//         'data' => $data,
//     ]);
// });

// Route::get('/cache-test', function () {
//     $cacheKey = 'test_database_cache';
//     if (Cache::has($cacheKey)) {
//         $data = Cache::get($cacheKey);
//         return response()->json([
//             'source' => 'DATABASE CACHE',
//             'data' => $data,
//         ]);
//     }
//     $data = [
//         'id' => 101,
//         'name' => 'Database Cache Test',
//         'created_at' => now()->toDateTimeString(),
//     ];
//     Cache::put($cacheKey, $data, now()->addMinutes(10));
//     return response()->json([
//         'source' => 'NEW DATA STORED',
//         'data' => $data,
//     ]);
// });


Route::get('/', function () {
    dispatch( new \App\Jobs\SendEmailJob());
    return view('welcome');
});

Route::get('/cache-redis', function () {
    // Store in Redis Cache
    Cache::put('user_name', 'Rohit', 600); // 600 seconds

    // Retrieve from Redis Cache
    $value = Cache::get('user_name');

    return 'Cached Value: ' . $value;
});

<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Bus;
use App\Jobs\SampleTaskJob;
use Illuminate\Support\Facades\Log;
use App\Jobs\DelayJob;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\Http;

Route::get('/cache-test', function () {
    $cacheKey = 'test_data';
    if (Cache::has($cacheKey)) {
        $data = Cache::get($cacheKey);
        return response()->json([
            'source' => 'CACHE',
            'data' => $data,
        ]);
    }
    $data = [
        'id' => 1,
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'created_at' => now()->toDateTimeString(),
    ];
    Cache::put($cacheKey, $data, now()->addMinutes(5));
    return response()->json([
        'source' => 'NEW DATA (stored in cache)',
        'data' => $data,
    ]);
});

Route::get('/cache-test-db', function () {
    $cacheKey = 'test_database_cache';
    if (Cache::has($cacheKey)) {
        $data = Cache::get($cacheKey);
        return response()->json([
            'source' => 'DATABASE CACHE',
            'data' => $data,
        ]);
    }
    $data = [
        'id' => 101,
        'name' => 'Database Cache Test',
        'created_at' => now()->toDateTimeString(),
    ];
    Cache::put($cacheKey, $data, now()->addMinutes(10));
    return response()->json([
        'source' => 'NEW DATA STORED',
        'data' => $data,
    ]);
});

Route::get('/', function () {
    dispatch(new \App\Jobs\SendEmailJob());
    return view('welcome');
});

Route::get('/cache-redis', function () {

    Cache::put('user_name', 'Rohit', 600);

    $value = Cache::get('user_name');

    return 'Cached Value: ' . $value;
});

Route::get('/run-batch', function () {
    $jobs = [];

    for ($i = 1; $i <= 5; $i++) {
        $jobs[] = new SampleTaskJob($i);
    }

    Bus::batch($jobs)
        ->then(function () {
            Log::info('✅ All tasks finished successfully.');
        })
        ->catch(function () {
            Log::error('❌ Some tasks failed.');
        })
        ->dispatch();

    return 'Batch jobs dispatched! Check log file.';
});

Route::get('/run-sequential', function () {
    $start = microtime(true);

    foreach (range(1, 3) as $i) {
        DelayJob::dispatchSync($i);
    }

    $end = microtime(true);
    return response()->json(['time' => round($end - $start, 2)]);
});

Route::get('/run-concurrent', function () {
    $start = microtime(true);

    Bus::batch([
        new DelayJob(1),
        new DelayJob(2),
        new DelayJob(3),
    ])->dispatch();

    $end = microtime(true);
    return response()->json(['time' => round($end - $start, 2)]);
});

Route::get('/sequential-test', function () {
    $start = microtime(true);

    $results = [
        tap('Task 1', fn() => sleep(2)),
        tap('Task 2', fn() => sleep(2)),
        tap('Task 3', fn() => sleep(2)),
    ];

    $end = microtime(true);

    return response()->json([
        'results' => $results,
        'time_taken' => round($end - $start, 2) . ' seconds',
    ]);
});

Route::get('/concurrency-test', function () {
    $start = microtime(true);

    $results = Concurrency::run([
        fn() => tap('Task 1', function () {
            sleep(2);
            Log::info('Task 1 done');
        }),
        fn() => tap('Task 2', function () {
            sleep(2);
            Log::info('Task 2 done');
        }),
        fn() => tap('Task 3', function () {
            sleep(2);
            Log::info('Task 3 done');
        }),
    ]);

    $end = microtime(true);

    return response()->json([
        'results' => $results,
        'time_taken' => round($end - $start, 2) . ' seconds',
    ]);
});

// Route::get('/concurrency-http', function () {
//     $start = microtime(true);

//     $results = Concurrency::run([
//         fn() => file_put_contents('file1.txt', str_repeat('A', 100000)),
//         fn() => file_put_contents('file2.txt', str_repeat('B', 100000)),
//     ]);

//     $end = microtime(true);

//     return response()->json([
//         'results' => collect($results)->pluck('title'),
//         'time_taken' => round($end - $start, 2) . ' seconds',
//     ]);
// });


Route::get('/http-sequential', function () {
    $start = microtime(true);

    $res1 = Http::get('https://jsonplaceholder.typicode.com/posts/1')->json();
    $res2 = Http::get('https://jsonplaceholder.typicode.com/posts/2')->json();
    $res3 = Http::get('https://jsonplaceholder.typicode.com/posts/3')->json();

    $end = microtime(true);

    return response()->json([
        'titles' => [$res1['title'], $res2['title'], $res3['title']],
        'type' => 'Sequential',
        'time_taken' => round($end - $start, 2) . ' seconds',
    ]);
});

Route::get('/http-pool', function () {
    $start = microtime(true);

    $responses = Http::pool(fn($pool) => [
        $pool->as('first')->get('https://jsonplaceholder.typicode.com/posts/1'),
        $pool->as('second')->get('https://jsonplaceholder.typicode.com/posts/2'),
        $pool->as('third')->get('https://jsonplaceholder.typicode.com/posts/3'),
    ]);

    $end = microtime(true);

    return response()->json([
        'titles' => [
            $responses['first']->json()['title'],
            $responses['second']->json()['title'],
            $responses['third']->json()['title'],
        ],
        'time_taken' => round($end - $start, 2) . ' seconds',
    ]);
});

// Error Response cURL error 6 --> We can Use Pool to Work Properly ( /http-pool )
Route::get('/http-concurrent', function () {
    $start = microtime(true);

    try {
        $results = Concurrency::run([
            fn() => Http::get('https://jsonplaceholder.typicode.com/posts/1')->json(),
            fn() => Http::get('https://jsonplaceholder.typicode.com/posts/2')->json(),
            fn() => Http::get('https://jsonplaceholder.typicode.com/posts/3')->json(),
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error executing concurrent requests: ' . $e->getMessage()]);
    }

    $end = microtime(true);

    return response()->json([
        'titles' => collect($results)->pluck('title'),
        'type' => 'Concurrent',
        'time_taken' => round($end - $start, 2) . ' seconds',
    ]);
});

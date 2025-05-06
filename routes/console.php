<?php

use App\Console\Commands\DeleteOldPosts;
use App\Models\Post;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(DeleteOldPosts::class)->everyTenSeconds();

Schedule::call(function () {
    $deleted = DB::table('posts')->limit(10)->delete();

    if ($deleted === 0) {
        Log::info('Unable to find post');
    } else {
        Log::info("Deleted $deleted old posts.");
    }
})->everyTenSeconds();
    
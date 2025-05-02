<?php

namespace Rohit\V1;

use Illuminate\Support\ServiceProvider;

class V1ServiceProvider extends ServiceProvider
{
    public function register()
    {
        require_once __DIR__ . '/Helpers.php';
    }

    public function boot()
    {

    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;

/* Processes in Laravel refer to the ability to run external system commands or scripts from your Laravel application—like running a terminal command directly from PHP using Laravel’s clean syntax. */

class SystemController extends Controller
{
    public function runCommand()
    {
        $result = Process::path(base_path())->run('php artisan list');

        return response()->json([
            'output' => $result->output(),
            'success' => $result->successful(),
            'error' => $result->errorOutput()
        ]);
    }

    public function makeController(Request $request)
    {
        $name = $request->query('name');

        if (empty($name)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a controller name using ?name=YourControllerName'
            ]);
        }

        $result = Process::path(base_path())->run("php artisan make:controller {$name}");

        return response()->json([
            'command' => "php artisan make:controller {$name}",
            'output' => $result->output(),
            'success' => $result->successful(),
            'error' => $result->errorOutput(),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        Log::info('User accessed the home page.');

        return response()->json(['message' => 'Welcome to the home page!']);
    }
}

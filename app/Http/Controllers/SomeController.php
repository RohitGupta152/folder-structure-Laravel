<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use App\Jobs\LogSomethingJob;

class SomeController extends Controller
{
    public function dispatchJob()
    {
        $context = App::make('request-context'); // retrieve from container
        LogSomethingJob::dispatch($context);

        return response()->json(['status' => 'Job dispatched']);
    }
}

<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SampleTaskJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public $taskNumber;

    public function __construct($taskNumber)
    {
        $this->taskNumber = $taskNumber;
    }

    public function handle()
    {
        sleep(2);
        Log::info("✅ Task {$this->taskNumber} completed by Job ID: " . $this->job->getJobId());
    }
}

<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportProductJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected $product;

    public function __construct($product)
    {
        $this->product = $product;
    }

    public function handle()
    {
        Log::info("✅ Importing product: " . $this->product);
        // throw new \Exception("❌ Failed to import: " . $this->product); // Uncomment to test failure
    }
}

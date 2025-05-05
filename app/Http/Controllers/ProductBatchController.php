<?php

namespace App\Http\Controllers;

use App\Jobs\ImportProductJob;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductBatchController extends Controller
{
    public function import()
    {
        $product1 = 'iPhone';
        $product2 = 'Samsung';
        $product3 = 'OnePlus';

        Bus::batch([
            new ImportProductJob($product1),
            new ImportProductJob($product2),
            new ImportProductJob($product3),
        ])
            ->then(function (Batch $batch) {
                Log::info('🎉 All products imported successfully.');
            })
            ->catch(function (Batch $batch, Throwable $e) {
                Log::error('❌ A job failed in the batch: ' . $e->getMessage());
            })
            ->finally(function (Batch $batch) {
                Log::info('🔚 Batch processing complete.');
            })
            ->dispatch();

        return response()->json(['message' => 'Batch import started!']);
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseQueryPerformanceCommand extends Command
{
    protected $signature = 'db:performance 
        {--count=100 : Number of test queries}';
    
    protected $description = 'Test database query performance';

    public function handle()
    {
        $count = $this->option('count');
        
        $startTime = microtime(true);
        
        $this->withProgressBar($count, function () {
            DB::table('users')->first();
        });

        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 4);

        $this->newLine(2);
        $this->info("Performance Results:");
        $this->table('Metrics', [
            ['Metric', 'Value'],
            ['Total Queries', $count],
            ['Execution Time', "{$executionTime} seconds"],
            ['Average Query Time', round($executionTime / $count, 4) . " seconds"]
        ]);

        Log::info("DB Performance Test", [
            'total_queries' => $count,
            'execution_time' => $executionTime
        ]);
    }
}

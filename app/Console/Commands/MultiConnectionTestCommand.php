<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class MultiConnectionTestCommand extends Command
{
    protected $signature = 'db:connections';
    protected $description = 'Test all configured database connections';

    public function handle()
    {
        $connections = array_keys(Config::get('database.connections'));
        
        $results = [];

        foreach ($connections as $connection) {
            try {
                DB::connection($connection)->getPdo();
                $results[] = [
                    'Connection' => $connection,
                    'Status' => '✅ Connected',
                    'Database' => DB::connection($connection)->getDatabaseName()
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'Connection' => $connection,
                    'Status' => '❌ Failed',
                    'Database' => 'N/A'
                ];
            }
        }

        $this->table(['Connection', 'Status', 'Database'], $results);
    }
}

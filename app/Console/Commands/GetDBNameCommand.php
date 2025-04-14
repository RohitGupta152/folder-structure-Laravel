<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetDBNameCommand extends Command
{
    protected $signature = 'db:name';
    protected $description = 'Get current database name';

    public function handle()
    {
        $dbName = DB::connection()->getDatabaseName();
        $this->info("Current Database: {$dbName}");
    }
}

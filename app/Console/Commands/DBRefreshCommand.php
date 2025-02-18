<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class DBRefreshCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:fresh {--seed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh all the databases of the application main and tenants';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Tenant::all()->runForEach(function ($tenant) {
            $tenant->delete();
        });

        $this->call('migrate:fresh', ['--seed' => $this->option('seed')]);
    }
}

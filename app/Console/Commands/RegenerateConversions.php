<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RegenerateConversions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:regenerate-conversions
                                {--only-missing : Regenerate only missing conversions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate the derived images of media for every tenant';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Tenant::all()->runForEach(function () {
            $this->line('regenerate for tenant: ' . tenant()->id);

            $this->call('media-library:regenerate', [
                '--only-missing' => $this->option('only-missing')
            ]);
        });
    }
}

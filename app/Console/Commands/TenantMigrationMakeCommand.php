<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class TenantMigrationMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:tenant-migration {name : The name of the migration}
    {--create= : The table to be created}
    {--table= : The table to migrate}
    {--path=database/migrations/tenant : The location where the migration file should be created the default is tenant folder under migrations folder}
    {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
    {--fullpath : Output the full path of the migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration file';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $options = array_filter($this->options());

        $options = array_combine(array_map(function ($key) {
            return '--' . $key;
        }, array_keys($options)), $options);

        $this->call('make:migration', [
            'name' => $this->argument('name')
        ] + $options);
    }
}

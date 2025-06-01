<?php

/**
 * @author TIL <til@army.mil.bd>
 *
 * @contributor Md. Mostafijur Rahman <[mostafijur.til@gmail.com]>
 *
 * @created 01-06-2025
 */

 

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Seed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install {--seed=all} {--migrate=true} {--dummydata=true}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('migrate')) {
            try {
                $this->info('Migrating using schema...');
                $this->call('migrate:fresh');
                $this->info('Migration using schema successful.');
            } catch (\Exception $e) {
                $this->warn('Schema migration failed. Falling back to migration files...');
                $this->call('migrate:fresh');
                $this->info('Migration using migration files successful.');
            }
        }

        $this->mainAppSeed();

        $this->info('Database seeding completed successfully.');

        // $this->warn('Copying seed files...');
        // $this->copySeedFiles();
        // $this->info('Copied seed files successfully.');

        $this->call('cache:clear');
        $this->call('view:clear');
        $this->call('config:clear');
        $this->call('route:clear');
    }

    /*
    * Main App Seed
    *
    * @return void
    */
    protected function mainAppSeed()
    {
        if ($this->option('dummydata') == 'false') {
            $this->call('db:seed', ['--class' => 'Database\\Seeders\\DatabaseWithoutDummyDataSeeder']);
        }

        if ($this->option('dummydata') == 'true') {
            $this->call('db:seed');
        }
    }

    /*
    * Copy Seed Files
    *
    * @return void
    */
    protected function copySeedFiles()
    {
        \File::copyDirectory( public_path('seeder/') , public_path('uploads/'));
    }
}

<?php

namespace Reklid\SeedOnce\Console\Commands;

use Reklid\SeedOnce\Services\SeederManager;
use Illuminate\Console\Command;

class DatabaseSeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:once
                            {--class= : seeder name}
                            {--no-exec : only check uploaded seeders}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seeding database';

    public function __construct(protected SeederManager $manager)
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
        try {
            if ($this->option('no-exec')) {
                $this->manager->updateRegistry();
            } elseif ($class = $this->option('class')) {
                $this->manager->seedByClass($class);
            } else {
                $this->manager->seed();
            }

            $this->info('Seed completed');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}

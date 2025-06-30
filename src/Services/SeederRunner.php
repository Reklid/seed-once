<?php

namespace Reklid\SeedOnce\Services;

use Illuminate\Container\Container;
use Illuminate\Database\Seeder;

class SeederRunner extends Seeder
{
    public function __construct(Container $container)
    {
        $this->setContainer($container);
    }

    public function run(array $seeders): void
    {
        $this->call($seeders);
    }
}

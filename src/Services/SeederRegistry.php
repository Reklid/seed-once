<?php

namespace Reklid\SeedOnce\Services;

use Reklid\SeedOnce\Models\Seeder;
use Illuminate\Support\Facades\DB;

class SeederRegistry
{
    public function getExecuted(): array
    {
        return Seeder::query()->pluck('seeder')->toArray();
    }

    public function markAsExecuted(array $seeders): void
    {
        $batch = Seeder::query()->max('batch') + 1;

        $insert = array_reduce($seeders, function ($res, $seeder) use ($batch) {
            $res[] = ['seeder' => $seeder, 'batch' => $batch];
            return $res;
        }, []);

        DB::table('seeders')->insert($insert);
    }
}

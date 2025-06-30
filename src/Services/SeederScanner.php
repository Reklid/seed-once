<?php

namespace Reklid\SeedOnce\Services;

use SplFileInfo;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SeederScanner
{
    public function __construct(
        protected SeederRegistry $registry,
        protected string $path = '',
        protected string $namespace = 'Database\\Seeders',
    ) {
        $this->path = $this->path ?: database_path('seeders/');
    }

    private function getAllSeeders(): array
    {
        return collect(File::allFiles($this->path))
            ->filter(fn(SplFileInfo $file) =>
                $file->getExtension() === 'php' &&
                $file->getFilename() !== 'DatabaseSeeder.php'
            )
            ->map(fn(SplFileInfo $file) => $this->classNameFromFile($file))
            ->filter(fn(string $class) => class_exists($class))
            ->sortBy(fn($class) => $class::$sort ?? 9999)
            ->values()
            ->all();
    }

    public function getPendingSeeders(): array
    {
        $executed = $this->registry->getExecuted();

        return collect($this->getAllSeeders())
            ->reject(fn($class) =>
                in_array($class, $executed) || ($class::$disabled ?? false)
            )
            ->values()
            ->all();
    }

    private function classNameFromFile(SplFileInfo $file): string
    {
        $relative = Str::of($file->getPathname())
            ->after($this->path)
            ->replace('.php', '')
            ->replace('/', '\\')
            ->replace('\\', '\\');

        return $this->namespace . '\\' . $relative;
    }
}

<?php

namespace Reklid\SeedOnce\Services;

class SeederManager
{
    public function __construct(
        protected SeederScanner $scanner,
        protected SeederRegistry $registry,
        protected SeederRunner $runner
    ) {}

    /**
     * Запускает только новые и не отключённые сидеры.
     */
    public function seed(): void
    {
        $seedersToRun = $this->scanner->getPendingSeeders();

        if (!empty($seedersToRun)) {
            $this->runner->run($seedersToRun);
            $this->registry->markAsExecuted($seedersToRun);
        }
    }

    /**
     * Отмечает новые и не отключённые сидеры как выполненные (без запуска).
     */
    public function updateRegistry(): void
    {
        $seedersToMark = $this->scanner->getPendingSeeders();

        if (!empty($seedersToMark)) {
            $this->registry->markAsExecuted($seedersToMark);
        }
    }

    /**
     * Запускает конкретный сидер, если он не был выполнен ранее.
     */
    public function seedByClass(string $class): void
    {
        throw_if(!class_exists($class), \Exception::class, "Seeder [$class] not found", 404);

        $executed = $this->registry->getExecuted();

        if (!in_array($class, $executed) && !($class::$disabled ?? false)) {
            $this->runner->run([$class]);
            $this->registry->markAsExecuted([$class]);
        }
    }
}

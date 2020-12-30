<?php declare(strict_types=1);

use DI\Container;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\MigrationManager;

/** @var Container $container */
$container = require __DIR__ . '/../config/dependency-injection.php';

try {
    $migrationManager = $container->get(MigrationManager::class);
    $migrationManager->run();
    $migrationManager->executeMigrations();
    $migrationManager->unlock();

    echo 'Migrations ran successfully';
} catch (Throwable $e) {
    echo $e->getMessage();
}
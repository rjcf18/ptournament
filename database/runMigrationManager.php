<?php declare(strict_types=1);

use DI\Container;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\MigrationManager;

/** @var Container $container */
$container = require __DIR__ . '/../config/dependency-injection.php';

try {
    $migrationsManager = $container->get(MigrationManager::class);
    $migrationsManager->run();
    $migrationsManager->executeMigrations();
    $migrationsManager->unlock();

    echo 'Migrations ran successfully';
} catch (Throwable $e) {
    echo $e->getMessage();
}
<?php declare(strict_types=1);

use DI\Container;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\SchemaUpdateManager;

/** @var Container $container */
$container = require __DIR__ . '/../config/dependency-injection.php';

try {
    /** @var SchemaUpdateManager $schemaManager */
    $schemaManager = $container->get(SchemaUpdateManager::class . '_seeds');
    $schemaManager->initialize();
    $schemaManager->executeUpdates();
    $schemaManager->unlock();

    echo 'Seeds ran successfully';
} catch (Throwable $e) {
    echo $e->getMessage();
}
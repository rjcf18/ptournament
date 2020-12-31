<?php declare(strict_types=1);

use DI\Container;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\SeedsManager;

/** @var Container $container */
$container = require __DIR__ . '/../config/dependency-injection.php';

try {
    $seedsManager = $container->get(SeedsManager::class);
    $seedsManager->run();
    $seedsManager->executeSeeds();
    $seedsManager->unlock();

    echo 'Seeds ran successfully';
} catch (Throwable $e) {
    echo $e->getMessage();
}
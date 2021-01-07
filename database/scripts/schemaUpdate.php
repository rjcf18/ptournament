<?php declare(strict_types=1);

use DI\Container;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\SchemaUpdateManager;

/** @var Container $container */
$container = require __DIR__ . '/../../config/dependency-injection.php';

$schemaUpdateTypes = [
    'migrations',
    'seeds',
];

try {

    $commandOptions = getopt('', ['type:']);

    $schemaUpdateType = $commandOptions['type'] ?? '';

    if (empty($commandOptions) || empty($schemaUpdateType)) {
        throw new Exception(
            sprintf(
                'Please specify which is the schema update type by using --type=type in the command (types: %s)',
                implode(', ', $schemaUpdateTypes)
            )
        );
    }

    if (!in_array($schemaUpdateType, $schemaUpdateTypes)) {
        throw new Exception(
            sprintf('Please specify a valid schema update type (types: %s)', implode(', ', $schemaUpdateTypes))
        );
    }

    /** @var SchemaUpdateManager $schemaManager */
    $schemaManager = $container->get(sprintf('%s_%s', SchemaUpdateManager::class, $schemaUpdateType));
    $schemaManager->initialize();
    $schemaManager->executeUpdates();
    $schemaManager->unlock();

    echo ucfirst($schemaUpdateType) . ' ran successfully';
} catch (Throwable $e) {
    echo $e->getMessage();
}
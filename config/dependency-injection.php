<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Connection;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\ConnectionConfig;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\MigrationManager;
use DI\ContainerBuilder;

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(
    [
        // Bind an interface to an implementation
//        Interface::class => create(Implementation::class),

        ConnectionConfig::class => DI\factory(
            function () {
                return ConnectionConfig::createFromFile(__DIR__ . '/database.yml');
            }
        ),
        Connection::class => DI\create()
            ->constructor(DI\get(ConnectionConfig::class)),
        MigrationManager::class => DI\create()
            ->constructor(DI\get(Connection::class), __DIR__ . '/../database/migrations')
    ]
);

return $containerBuilder->build();
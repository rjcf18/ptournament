<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Connection;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\ConnectionConfig;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\MigrationManager;
use DI\ContainerBuilder;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\SeedsManager;
use PoolTournament\Application\Module\Friend\Infrastructure\Database\MySQL\Friend\Repository as MySQLFriendRepository;
use PoolTournament\Domain\Module\Friend\FetchInfo\FriendRepository as FriendRepositoryContract;

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(
    [
        ConnectionConfig::class => DI\factory(
            function () {
                return ConnectionConfig::createFromFile(__DIR__ . '/database.yml');
            }
        ),
        Connection::class => DI\create()
            ->constructor(DI\get(ConnectionConfig::class)),
        MigrationManager::class => DI\create()
            ->constructor(DI\get(Connection::class), __DIR__ . '/../database/migrations'),
        SeedsManager::class => DI\create()
            ->constructor(DI\get(Connection::class), __DIR__ . '/../database/seeds'),
        FriendRepositoryContract::class => DI\autowire(MySQLFriendRepository::class),
    ]
);

return $containerBuilder->build();
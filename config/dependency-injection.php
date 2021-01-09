<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use DI\ContainerBuilder;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Connection;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\ConnectionConfig;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\SchemaUpdateManager;
use PoolTournament\Application\Module\Friend\Infrastructure\Database\MySQL\Friend\Repository
    as MySQLFriendFetchInfoRepository;
use PoolTournament\Application\Module\Match\Infrastructure\Database\MySQL\Match\FetchInfo\Repository
    as MySQLMatchFetchInfoRepository;
use PoolTournament\Application\Module\Match\Infrastructure\Database\MySQL\Match\Creation\MatchRepository
    as MySQLMatchCreationRepository;
use PoolTournament\Application\Module\Match\Infrastructure\Database\MySQL\Match\Creation\FriendRepository
    as MySQLMatchCreationFriendRepository;
use PoolTournament\Domain\Module\Friend\FetchInfo\FriendRepository as FriendFetchInfoRepositoryContract;
use PoolTournament\Domain\Module\Match\FetchInfo\MatchRepository as MatchFetchInfoRepositoryContract;
use PoolTournament\Domain\Module\Match\Creation\MatchRepository as MatchCreationMatchRepositoryContract;
use PoolTournament\Domain\Module\Match\Creation\FriendRepository as MatchCreationFriendRepositoryContract;

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
        SchemaUpdateManager::class . '_migrations' => DI\create(SchemaUpdateManager::class)
            ->constructor(DI\get(Connection::class), __DIR__ . '/../database/migrations'),
        SchemaUpdateManager::class . '_seeds'=> DI\create(SchemaUpdateManager::class)
            ->constructor(DI\get(Connection::class), __DIR__ . '/../database/seeds'),
        FriendFetchInfoRepositoryContract::class => DI\autowire(MySQLFriendFetchInfoRepository::class),
        MatchFetchInfoRepositoryContract::class => DI\autowire(MySQLMatchFetchInfoRepository::class),
        MatchCreationMatchRepositoryContract::class => DI\autowire(MySQLMatchCreationRepository::class),
        MatchCreationFriendRepositoryContract::class => DI\autowire(MySQLMatchCreationFriendRepository::class),
    ]
);

return $containerBuilder->build();

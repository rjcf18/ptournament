<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Friend\Infrastructure\Database\MySQL\Friend\FetchRanking;

use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Connection;
use PoolTournament\Application\Module\Friend\Infrastructure\Database\MySQL\Friend\FriendEntityFactory;
use PoolTournament\Domain\Module\Friend\FetchRanking\FriendCollection;
use PoolTournament\Domain\Module\Friend\FetchRanking\FriendRepository as FetchRankingFriendRepository;

class FriendRepository implements FetchRankingFriendRepository
{
    protected const FRIEND_TABLE_NAME = 'friend';

    protected Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function fetchRanking(): FriendCollection
    {
        $query = sprintf(
            "SELECT * FROM
                (SELECT * FROM `%s` ORDER BY `points` DESC) AS `friends_by_points`
                ORDER BY `balls` ASC;",
            self::FRIEND_TABLE_NAME
        );

        $statement = $this->connection->query($query, Connection::FETCH_ASSOC);
        $friends = $statement->fetchAll();

        $friendCollection = FriendCollection::create();

        foreach ($friends as $friend) {
            $friendCollection->addFriend(FriendEntityFactory::create($friend));
        }

        return $friendCollection;
    }
}

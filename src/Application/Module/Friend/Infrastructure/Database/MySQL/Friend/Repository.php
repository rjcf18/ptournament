<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Friend\Infrastructure\Database\MySQL\Friend;

use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Connection;
use PoolTournament\Domain\Module\Friend\Entity\Friend as FriendEntity;
use PoolTournament\Domain\Module\Friend\FetchInfo\FriendRepository;

class Repository implements FriendRepository
{
    protected const FRIEND_TABLE_NAME = 'friend';

    protected Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getById(int $id): ?FriendEntity
    {
        $query = sprintf(
            "SELECT * FROM `%s` WHERE `id_friend` = %s;",
            self::FRIEND_TABLE_NAME,
            $this->connection->quote((string) $id, Connection::PARAM_INT)
        );

        $statement = $this->connection->query($query, Connection::FETCH_ASSOC);
        $result = $statement->fetch();
        $friendArray = !empty($result) ? $result : [];

        return FriendEntityFactory::create($friendArray);
    }
}
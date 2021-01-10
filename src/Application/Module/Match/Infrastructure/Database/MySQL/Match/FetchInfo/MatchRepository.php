<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Match\Infrastructure\Database\MySQL\Match\FetchInfo;

use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Connection;
use PoolTournament\Application\Module\Match\Infrastructure\Database\MySQL\Match\MatchEntityFactory;
use PoolTournament\Domain\Module\Match\Entity\MatchEntity;
use PoolTournament\Domain\Module\Match\FetchInfo\MatchRepository as FetchInfoMatchRepository;

class MatchRepository implements FetchInfoMatchRepository
{
    protected const MATCH_TABLE_NAME = 'match';
    protected const FRIEND_TABLE_NAME = 'friend';

    protected Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getById(int $id): ?MatchEntity
    {
        $query = $this->getMatchByIdQuery($id);
        $statement = $this->connection->query($query, Connection::FETCH_ASSOC);
        $match = $statement->fetch();

        if (empty($match)) {
            return null;
        }

        $query = $this->getFriendByIdQuery((int) $match['winner_id']);
        $statement = $this->connection->query($query, Connection::FETCH_ASSOC);
        $winnerFriend = $statement->fetch();

        $query = $this->getFriendByIdQuery((int) $match['looser_id']);
        $statement = $this->connection->query($query, Connection::FETCH_ASSOC);
        $looserFriend = $statement->fetch();

        return MatchEntityFactory::create($match, $winnerFriend, $looserFriend);
    }

    private function getMatchByIdQuery(int $id): string
    {
        return sprintf(
            "SELECT * FROM `%s` WHERE `id_match` = %s;",
            self::MATCH_TABLE_NAME,
            $this->connection->quote((string)$id, Connection::PARAM_INT)
        );
    }

    private function getFriendByIdQuery(int $id): string
    {
        return sprintf(
            "SELECT * FROM `%s` WHERE `id_friend` = %s;",
            self::FRIEND_TABLE_NAME,
            $this->connection->quote((string)$id, Connection::PARAM_INT)
        );
    }
}

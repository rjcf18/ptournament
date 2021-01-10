<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Match\Infrastructure\Database\MySQL\Match\FetchList;

use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Connection;
use PoolTournament\Application\Module\Match\Infrastructure\Database\MySQL\Match\MatchEntityFactory;
use PoolTournament\Domain\Module\Match\FetchList\MatchCollection;
use PoolTournament\Domain\Module\Match\FetchList\MatchRepository as FetchListMatchRepository;

class MatchRepository implements FetchListMatchRepository
{
    protected const MATCH_TABLE_NAME = 'match';
    protected const FRIEND_TABLE_NAME = 'friend';

    protected Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function fetchListForFriend(int $friendId): MatchCollection
    {
        $query = sprintf(
            'SELECT * FROM `%1$s` WHERE `winner_id` = %2$s OR `looser_id` = %2$s;',
            self::MATCH_TABLE_NAME,
            $this->connection->quote((string) $friendId, Connection::PARAM_INT)
        );

        return $this->fetchMatches($query);
    }

    public function fetchAll(): MatchCollection
    {
        $query = sprintf(
            'SELECT * FROM `%s`;',
            self::MATCH_TABLE_NAME
        );

        return $this->fetchMatches($query);
    }

    private function fetchMatches(string $query): MatchCollection
    {
        $statement = $this->connection->query($query, Connection::FETCH_ASSOC);
        $matches = $statement->fetchAll() ?? [];

        $matchCollection = MatchCollection::create();

        foreach ($matches as $match) {
            $winnerFriend = $this->fetchWinnerFriend($match);
            $looserFriend = $this->fetchLooserFriend($match);

            $matchCollection->addMatch(MatchEntityFactory::create($match, $winnerFriend, $looserFriend));
        }

        return $matchCollection;
    }

    private function fetchWinnerFriend(array $match): array
    {
        $query = $this->getFriendByIdQuery((int) $match['winner_id']);
        $statement = $this->connection->query($query, Connection::FETCH_ASSOC);

        return $statement->fetch() ?? [];
    }

    private function fetchLooserFriend(array $match): array
    {
        $query = $this->getFriendByIdQuery((int) $match['looser_id']);
        $statement = $this->connection->query($query, Connection::FETCH_ASSOC);

        return $statement->fetch() ?? [];
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

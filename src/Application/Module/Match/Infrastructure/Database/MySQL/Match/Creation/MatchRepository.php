<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Match\Infrastructure\Database\MySQL\Match\Creation;

use DateTimeImmutable;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Connection;
use PoolTournament\Domain\Module\Match\Creation\DTO\Request as RequestDTO;
use PoolTournament\Domain\Module\Match\Creation\MatchRepository as CreationMatchRepository;
use PoolTournament\Domain\Module\Match\Entity\MatchEntity;

class MatchRepository implements CreationMatchRepository
{
    protected const MATCH_TABLE_NAME = 'match';
    protected const FRIEND_TABLE_NAME = 'friend';

    protected Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function create(RequestDTO $creationDTO): ?MatchEntity
    {
        $query = sprintf(
            'INSERT INTO `%s` 
          (`winner_id`, `looser_id`, `looser_balls_left`, `absence`, `date`, `created_at`, `updated_at`)
          VALUES
          (:winner_id, :looser_id, :looser_balls_left, :absence, :date, :created_at, :updated_at);',
            self::MATCH_TABLE_NAME
        );

        $statement = $this->connection->prepare($query);
        $insertResult = $statement->execute([
            'winner_id' => $creationDTO->getWinnerId(),
            'looser_id' => $creationDTO->getLooserId(),
            'looser_balls_left' => $creationDTO->getLooserBallsLeft(),
            'absence' => $creationDTO->isMatchAbsence() ? 1 : 0,
            'date' => $creationDTO->getDate()->format('Y-m-d H:i:s'),
            'created_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            'updated_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]);

        return !empty($insertResult) ? $this->getById((int) $this->connection->lastInsertId()) : null;
    }

    private function getById(int $id): ?MatchEntity
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

    public function friendsAlreadyPlayed(int $friendId1, int $friendId2): bool
    {
        $query = sprintf(
            'SELECT * FROM `%1$s`
                WHERE (`winner_id` = %2$s AND `looser_id` = %3$s) OR (`winner_id` = %3$s AND `looser_id` = %2$s);',
            self::MATCH_TABLE_NAME,
            $this->connection->quote((string) $friendId1, Connection::PARAM_INT),
            $this->connection->quote((string) $friendId2, Connection::PARAM_INT),
        );

        $statement = $this->connection->query($query, Connection::FETCH_ASSOC);
        $match = $statement->fetch();

        return !empty($match);
    }
}

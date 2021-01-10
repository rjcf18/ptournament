<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Match\Infrastructure\Database\MySQL\Match\Creation;

use DateTimeImmutable;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Connection;
use PoolTournament\Application\Module\Match\Infrastructure\Database\MySQL\Match\FriendEntityFactory;
use PoolTournament\Domain\Module\Match\Creation\FriendRepository as CreationFriendRepository;
use PoolTournament\Domain\Module\Match\Entity\FriendEntity;

class FriendRepository implements CreationFriendRepository
{
    protected const FRIEND_TABLE_NAME = 'friend';

    protected Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function updateWinnerInfo(int $friendId, int $points): ?FriendEntity
    {
        $query = sprintf(
            'UPDATE `%s` SET
            `points` = `points` + :points,
            `updated_at` = :updated_at
            WHERE `id_friend` = :id_friend;',
            self::FRIEND_TABLE_NAME
        );

        $statement = $this->connection->prepare($query);
        $updateResult = $statement->execute([
            'points' => $points,
            'updated_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            'id_friend' => $friendId,
        ]);

        return !empty($updateResult) ? $this->getById($friendId) : null;
    }

    public function updateLooserInfo(int $friendId, int $points, int $ballsLeft): ?FriendEntity
    {
        $query = sprintf(
            'UPDATE `%s` SET
            `points` = `points` + :points,
            `balls` = `balls` + :balls_left,
            `updated_at` = :updated_at
            WHERE `id_friend` = :id_friend;',
            self::FRIEND_TABLE_NAME
        );

        $statement = $this->connection->prepare($query);
        $updateResult = $statement->execute([
            'points' => $points,
            'balls_left' => $ballsLeft,
            'updated_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            'id_friend' => $friendId,
        ]);

        return !empty($updateResult) ? $this->getById($friendId) : null;
    }

    private function getById(int $id): ?FriendEntity
    {
        $query = sprintf(
            "SELECT * FROM `%s` WHERE `id_friend` = %s;",
            self::FRIEND_TABLE_NAME,
            $this->connection->quote((string) $id, Connection::PARAM_INT)
        );

        $statement = $this->connection->query($query, Connection::FETCH_ASSOC);
        $friend = $statement->fetch();

        return $friend ? FriendEntityFactory::create($friend) : null;
    }
}

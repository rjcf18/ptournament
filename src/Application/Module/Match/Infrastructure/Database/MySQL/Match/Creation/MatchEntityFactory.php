<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Match\Infrastructure\Database\MySQL\Match\Creation;

use DateTimeImmutable;
use PoolTournament\Domain\Module\Match\Entity\MatchEntity;

class MatchEntityFactory
{
    public static function create(array $matchArray, array $winnerFriend, array $looserFriend): MatchEntity
    {
        return new MatchEntity(
            (int) $matchArray['id_match'],
            FriendEntityFactory::create($winnerFriend),
            FriendEntityFactory::create($looserFriend),
            (int) $matchArray['looser_balls_left'],
            (bool) $matchArray['absence'],
            DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $matchArray['date']),
            DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $matchArray['created_at']),
            DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $matchArray['updated_at'])
        );
    }
}

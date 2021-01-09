<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Match\Infrastructure\Database\MySQL\Match\Creation;

use DateTimeImmutable;
use PoolTournament\Domain\Module\Match\Entity\FriendEntity;

class FriendEntityFactory
{
    public static function create(array $friendArray): FriendEntity
    {
        return new FriendEntity(
            (int) $friendArray['id_friend'],
            $friendArray['name'],
            (int) $friendArray['points'],
            (int) $friendArray['balls'],
            DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $friendArray['created_at']),
            DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $friendArray['updated_at'])
        );
    }
}

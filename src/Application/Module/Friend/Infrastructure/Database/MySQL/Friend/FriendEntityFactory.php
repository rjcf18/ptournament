<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Friend\Infrastructure\Database\MySQL\Friend;

use DateTimeImmutable;
use PoolTournament\Domain\Module\Friend\Entity\Friend;

class FriendEntityFactory
{
    public static function create(array $friendArray): Friend
    {
        return new Friend(
            (int) $friendArray['id_friend'],
            $friendArray['name'],
            DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $friendArray['created_at']),
            DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $friendArray['updated_at'])
        );
    }
}
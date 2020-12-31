<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Friend\Entrypoint;

use PoolTournament\Domain\Module\Friend\Entity\Friend;

class FriendArrayBuilder
{
    public static function build(Friend $friendEntity): array
    {
        return [
            'id' => $friendEntity->getId(),
            'name' => $friendEntity->getName()
        ];
    }
}
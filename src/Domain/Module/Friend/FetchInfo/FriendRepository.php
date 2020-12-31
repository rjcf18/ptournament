<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Friend\FetchInfo;

use PoolTournament\Domain\Module\Friend\Entity\Friend as FriendEntity;

interface FriendRepository
{
    public function getById(int $id): ?FriendEntity;
}
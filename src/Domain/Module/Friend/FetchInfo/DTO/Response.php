<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Friend\FetchInfo\DTO;

use PoolTournament\Domain\Module\Friend\Entity\Friend as FriendEntity;

class Response
{
    private FriendEntity $friend;

    public function __construct(FriendEntity $friend)
    {
        $this->friend = $friend;
    }

    public function getFriend(): FriendEntity
    {
        return $this->friend;
    }
}
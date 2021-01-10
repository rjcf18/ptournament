<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Friend\FetchRanking;

use PoolTournament\Domain\Module\Friend\Entity\Friend as FriendEntity;

class FriendCollection
{
    /** @var FriendEntity[] */
    private array $friends = [];

    public static function create(): self
    {
        return new self();
    }

    public function addFriend(FriendEntity $friend): self
    {
        $this->friends[] = $friend;

        return $this;
    }

    public function getAll(): array
    {
        return $this->friends;
    }

    public function get(int $index): ?FriendEntity
    {
        return $this->friends[$index] ?? null;
    }
}

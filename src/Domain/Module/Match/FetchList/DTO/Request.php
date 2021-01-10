<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Match\FetchList\DTO;

class Request
{
    private ?int $friendId = null;

    public function getFriendId(): ?int
    {
        return $this->friendId;
    }

    public function setFriendId(int $friendId): self
    {
        $this->friendId = $friendId;

        return $this;
    }
}

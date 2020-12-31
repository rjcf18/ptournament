<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Friend\FetchInfo\DTO;

class Request
{
    private int $friendId;

    public function __construct(int $friendId)
    {
        $this->friendId = $friendId;
    }

    public function getFriendId(): int
    {
        return $this->friendId;
    }
}
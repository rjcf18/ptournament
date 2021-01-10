<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Friend\FetchRanking;

class ResponseDTO
{
    private FriendCollection $friendCollection;

    public function __construct(FriendCollection $friendCollection)
    {
        $this->friendCollection = $friendCollection;
    }

    public function getFriendCollection(): FriendCollection
    {
        return $this->friendCollection;
    }
}

<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Friend\FetchRanking;

interface FriendRepository
{
    public function fetchRanking(): FriendCollection;
}

<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Match\FetchList;

interface MatchRepository
{
    public function fetchListForFriend(int $friendId): MatchCollection;
    public function fetchAll(): MatchCollection;
}

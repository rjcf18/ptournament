<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Match\Creation;

use PoolTournament\Domain\Module\Match\Entity\FriendEntity;

interface FriendRepository
{
    public function updateWinnerInfo(int $friendId, int $points): ?FriendEntity;
    public function updateLooserInfo(int $friendId, int $points, int $ballsLeft): ?FriendEntity;
}

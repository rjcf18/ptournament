<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Match\Entrypoint;

use PoolTournament\Domain\Module\Match\Entity\FriendEntity;
use PoolTournament\Domain\Module\Match\Entity\MatchEntity;

class MatchArrayBuilder
{
    public static function build(MatchEntity $match): array
    {
        return [
            'id' => $match->getId(),
            'winner' => self::buildFriend($match->getWinner()),
            'looser' => self::buildFriend($match->getLooser()),
            'looser_balls_left' => $match->getLooserBallsLeft(),
            'absence' => $match->isAbsence(),
            'date' => $match->getDate()->format('Y-m-d H:i:s'),
        ];
    }

    private static function buildFriend(FriendEntity $friend): array
    {
        return [
            'id' => $friend->getId(),
            'name' => $friend->getName(),
            'points' => $friend->getPoints(),
            'balls' => $friend->getBalls(),
        ];
    }
}

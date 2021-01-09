<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Match\Creation\DTO;

use PoolTournament\Domain\Module\Match\Entity\MatchEntity;

class Response
{
    private MatchEntity $match;

    public function __construct(MatchEntity $match)
    {
        $this->match = $match;
    }

    public function getMatch(): MatchEntity
    {
        return $this->match;
    }
}

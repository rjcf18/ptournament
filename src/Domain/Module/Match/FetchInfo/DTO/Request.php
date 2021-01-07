<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Match\FetchInfo\DTO;

class Request
{
    private int $matchId;

    public function __construct(int $matchId)
    {
        $this->matchId = $matchId;
    }

    public function getMatchId(): int
    {
        return $this->matchId;
    }
}
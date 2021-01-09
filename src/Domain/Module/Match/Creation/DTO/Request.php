<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Match\Creation\DTO;

use DateTimeImmutable;

class Request
{
    private int $winnerId;
    private int $looserId;
    private int $looserBallsLeft;
    private bool $matchAbsence;
    private DateTimeImmutable $date;

    public function __construct(
        int $winnerId,
        int $looserId,
        int $looserBallsLeft,
        bool $matchAbsence,
        DateTimeImmutable $date
    ) {
        $this->winnerId = $winnerId;
        $this->looserId = $looserId;
        $this->looserBallsLeft = $looserBallsLeft;
        $this->matchAbsence = $matchAbsence;
        $this->date = $date;
    }

    public function getWinnerId(): int
    {
        return $this->winnerId;
    }

    public function getLooserId(): int
    {
        return $this->looserId;
    }

    public function getLooserBallsLeft(): int
    {
        return $this->looserBallsLeft;
    }

    public function isMatchAbsence(): bool
    {
        return $this->matchAbsence;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }
}

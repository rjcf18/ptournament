<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Match\FetchList;

use PoolTournament\Domain\Module\Match\Entity\MatchEntity;

class MatchCollection
{
    /** @var MatchEntity[] */
    private array $matches = [];

    public static function create(): self
    {
        return new self();
    }

    public function addMatch(MatchEntity $match): self
    {
        $this->matches[$match->getId()] = $match;

        return $this;
    }

    public function getAll(): array
    {
        return $this->matches;
    }

    public function get(int $matchId): ?MatchEntity
    {
        return $this->matches[$matchId] ?? null;
    }
}

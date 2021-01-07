<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Match\FetchInfo;

use PoolTournament\Domain\Module\Match\Entity\MatchEntity;

interface MatchRepository
{
    public function getById(int $id): ?MatchEntity;
}
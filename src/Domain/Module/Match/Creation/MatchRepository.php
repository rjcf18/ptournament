<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Match\Creation;

use PoolTournament\Domain\Module\Match\Creation\DTO\Request as RequestDTO;
use PoolTournament\Domain\Module\Match\Entity\MatchEntity;

interface MatchRepository
{
    public function create(RequestDTO $creationDTO): ?MatchEntity;
}

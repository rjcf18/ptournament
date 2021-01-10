<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Match\FetchList\DTO;

use PoolTournament\Domain\Module\Match\FetchList\MatchCollection;

class Response
{
    private MatchCollection $matchCollection;

    public function __construct(MatchCollection $matchCollection)
    {
        $this->matchCollection = $matchCollection;
    }

    public function getMatchCollection(): MatchCollection
    {
        return $this->matchCollection;
    }
}

<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Friend\Entrypoint\Http\Rest;

use PoolTournament\Domain\Module\Match\FetchList\MatchCollection;

class MatchCollectionArrayBuilder
{
    public static function build(MatchCollection $matchCollection): array
    {
        $matches = [];

        foreach ($matchCollection->getAll() as $match)
        {
            $matches[] = MatchArrayBuilder::build($match);
        }

        return $matches;
    }
}

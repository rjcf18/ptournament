<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Friend\Entrypoint\Http\Rest;

use PoolTournament\Domain\Module\Friend\FetchRanking\FriendCollection;

class FriendCollectionArrayBuilder
{
    public static function build(FriendCollection $friendCollection): array
    {
        $matches = [];

        foreach ($friendCollection->getAll() as $friend)
        {
            $matches[] = FriendArrayBuilder::build($friend);
        }

        return $matches;
    }
}

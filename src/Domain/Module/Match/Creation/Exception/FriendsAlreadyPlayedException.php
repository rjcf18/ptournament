<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Match\Creation\Exception;

use Exception;

class FriendsAlreadyPlayedException extends Exception
{
    public const MESSAGE = 'Match registration failed. The involved players already played a match before';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}

<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Match\Creation\Exception;

use Exception;

class MatchCreationErrorException extends Exception
{
    public const MESSAGE = 'There was an unexpected error while creating the new match';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}

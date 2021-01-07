<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Match\FetchInfo\Exception;

use Exception;

class MatchNotFoundException extends Exception
{
    public const MESSAGE = 'Match not found';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}
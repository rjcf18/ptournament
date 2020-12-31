<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Friend\FetchInfo\Exception;

use Exception;

class FriendNotFoundException extends Exception
{
    public const MESSAGE = 'Friend not found';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}
<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Match\Creation\Exception;

use Exception;

class LooserInfoUpdateErrorException extends Exception
{
    public const MESSAGE = 'There was an unexpected error while updating the looser friend info';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}

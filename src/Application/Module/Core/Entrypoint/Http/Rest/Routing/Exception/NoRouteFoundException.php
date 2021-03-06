<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\Exception;

use Exception;

class NoRouteFoundException extends Exception
{
    public const MESSAGE = 'No route found for the incoming request';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}
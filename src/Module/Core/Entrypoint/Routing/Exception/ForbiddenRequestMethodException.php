<?php declare(strict_types=1);
namespace PoolTournament\App\Module\Core\Entrypoint\Routing\Exception;

use Exception;

class ForbiddenRequestMethodException extends Exception
{
    public const MESSAGE = 'The request method %s is not allowed for this route (Methods: %s)';

    public function __construct(string $requestMethod, array $allowedMethods)
    {
        parent::__construct(sprintf(self::MESSAGE, $requestMethod, implode(', ', $allowedMethods)));
    }
}
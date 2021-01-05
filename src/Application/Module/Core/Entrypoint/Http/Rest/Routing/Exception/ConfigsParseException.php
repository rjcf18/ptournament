<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\Exception;

use Exception;

class ConfigsParseException extends Exception
{
    public const MESSAGE = 'An unexpected error occurred while parsing the routing configs file';

    public function __construct(string $filePath)
    {
        parent::__construct(sprintf('%s (%s)', static::MESSAGE, $filePath));
    }
}
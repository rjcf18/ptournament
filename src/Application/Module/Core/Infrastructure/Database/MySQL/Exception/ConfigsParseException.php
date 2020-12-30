<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Exception;

use Exception;

class ConfigsParseException extends Exception
{
    public const MESSAGE = 'An unexpected error occurred while parsing the database configs file';

    public function __construct(string $filePath)
    {
        parent::__construct(sprintf('%s (%s)', static::MESSAGE, $filePath));
    }
}
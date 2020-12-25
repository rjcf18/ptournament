<?php declare(strict_types=1);
namespace PoolTournament\App\Module\Core\Entrypoint\Yaml\Exception;

use Exception;

class ParseException extends Exception
{
    public const MESSAGE = 'An unexpected error occurred while parsing the YAML file';

    public function __construct(string $filePath)
    {
        parent::__construct(sprintf('%s (%s)', static::MESSAGE, $filePath));
    }
}
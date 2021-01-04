<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Exception;

use UnexpectedValueException;

class SchemaUpdateInvalidFormatException extends UnexpectedValueException
{
    public const MESSAGE = 'A schema update file uses an invalid filename format: %s (missing date part)';

    public function __construct(string $schemaUpdateFileName)
    {
        parent::__construct(
            sprintf(self::MESSAGE, $schemaUpdateFileName)
        );
    }
}
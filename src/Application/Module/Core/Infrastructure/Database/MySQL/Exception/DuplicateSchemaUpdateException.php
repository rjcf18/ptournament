<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Exception;

class DuplicateSchemaUpdateException extends SchemaUpdateException
{
    public const MESSAGE = 'Duplicate schema update name: %s (file: %s, already declared in: %s)';

    public function __construct(
        string $schemaUpdateClassName,
        string $schemaUpdateFileName,
        string $usedSchemaUpdateFileName
    ) {
        parent::__construct(
            sprintf(self::MESSAGE, $schemaUpdateClassName, $schemaUpdateFileName, $usedSchemaUpdateFileName)
        );
    }
}
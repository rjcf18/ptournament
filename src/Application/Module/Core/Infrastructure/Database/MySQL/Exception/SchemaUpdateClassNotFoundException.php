<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Exception;

class SchemaUpdateClassNotFoundException extends SchemaUpdateException
{
    public const MESSAGE = 'Schema update class not found: %s (file: %s)';

    public function __construct(
        string $schemaUpdateClassName,
        string $schemaUpdateFileName
    ) {
        parent::__construct(
            sprintf(self::MESSAGE, $schemaUpdateClassName, $schemaUpdateFileName)
        );
    }
}
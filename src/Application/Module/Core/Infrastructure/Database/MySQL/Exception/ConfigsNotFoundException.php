<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Exception;

class ConfigsNotFoundException extends ConfigsParseException
{
    public const MESSAGE = 'The database configs file was not found';
}
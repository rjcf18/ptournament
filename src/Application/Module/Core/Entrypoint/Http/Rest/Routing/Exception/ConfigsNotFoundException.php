<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Core\Entrypoint\Http\Rest\Routing\Exception;

class ConfigsNotFoundException extends ConfigsParseException
{
    public const MESSAGE = 'The routing configs file was not found';
}
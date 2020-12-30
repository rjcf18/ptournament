<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Core\Entrypoint\Yaml\Exception;

class FileNotFoundException extends ParseException
{
    public const MESSAGE = 'The YAML file was not found';
}
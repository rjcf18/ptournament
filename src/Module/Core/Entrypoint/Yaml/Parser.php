<?php declare(strict_types=1);
namespace PoolTournament\App\Module\Core\Entrypoint\Yaml;

use PoolTournament\App\Module\Core\Entrypoint\Yaml\Exception\FileNotFoundException;
use PoolTournament\App\Module\Core\Entrypoint\Yaml\Exception\ParseException;

class Parser
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @throws ParseException
     *
     * @return array
     */
    public function parse(): array
    {
        if (!is_file($this->filePath)) {
            throw new FileNotFoundException($this->filePath);
        }

        $parseResult = yaml_parse_file($this->filePath);

        if (empty($parseResult)) {
            throw new ParseException($this->filePath);
        }

        return (array) $parseResult;
    }
}
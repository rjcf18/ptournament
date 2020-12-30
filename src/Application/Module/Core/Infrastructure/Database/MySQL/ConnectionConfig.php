<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL;

use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Exception\ConfigsNotFoundException;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Exception\ConfigsParseException;

class ConnectionConfig
{
    private string $host;
    private string $database;
    private string $username;
    private string $password;
    private array $options = [];

    public function __construct(string $host, string $database, string $username, string $password)
    {
        $this->host = $host;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @param string $filePath
     *
     * @throws ConfigsParseException
     *
     * @return self
     */
    public static function createFromFile(string $filePath): self
    {
        if (!is_file($filePath)) {
            throw new ConfigsNotFoundException($filePath);
        }

        $configs = yaml_parse_file($filePath);

        if (empty($configs)) {
            throw new ConfigsParseException($filePath);
        }

        $configs = (array) $configs;

        return new self($configs['host'], $configs['database'], $configs['username'], $configs['password']);
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): ConnectionConfig
    {
        $this->options = $options;

        return $this;
    }
}
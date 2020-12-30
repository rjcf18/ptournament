<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL;

use PDO;

class Connection extends PDO
{
    private const MYSQL_DATA_SOURCE_NAME_FORMAT = 'mysql:host=%s;dbname=%s';

    public function __construct(ConnectionConfig $connectionConfig)
    {
        parent::__construct(
            sprintf(self::MYSQL_DATA_SOURCE_NAME_FORMAT, $connectionConfig->getHost(), $connectionConfig->getDatabase()),
            $connectionConfig->getUsername(),
            $connectionConfig->getPassword(),
            $connectionConfig->getOptions()
        );
    }
}
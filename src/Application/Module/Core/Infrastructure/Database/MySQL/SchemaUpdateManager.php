<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL;

use DateTimeImmutable;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Exception\DuplicateSchemaUpdateException;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Exception\SchemaUpdateException;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Exception\SchemaUpdateLockException;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Exception\SchemaUpdateClassNotFoundException;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Exception\SchemaUpdateInvalidFormatException;

class SchemaUpdateManager
{
    private const TABLE_NAME = 'schema_update';

    protected Connection $connection;
    protected string $updatesPath;
    protected array $updates = [];
    protected array $updatesToExecute = [];
    protected array $executedUpdates = [];

    public function __construct(Connection $connection, string $updatesPath)
    {
        $this->connection = $connection;
        $this->updatesPath = $updatesPath;
    }

    /**
     * @throws DuplicateSchemaUpdateException
     * @throws SchemaUpdateException
     * @throws SchemaUpdateLockException
     * @throws SchemaUpdateClassNotFoundException
     */
    public function initialize(): void
    {
        $this->initializeTable();
        $this->lock();

        try {
            $this->loadUpdates();
            $this->loadExecutedUpdates();
            $this->selectUpdatesToExecute();
        } catch (SchemaUpdateException $e) {
            $this->unlock();

            throw $e;
        }
    }

    protected function initializeTable(): void
    {
        $query = sprintf(
            'CREATE TABLE IF NOT EXISTS `%1$s` (
                `id_%1$s` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NOT NULL,
                `created_at` DATETIME NOT NULL,
                `executed_at` DATETIME NULL DEFAULT NULL,
                PRIMARY KEY (`id_%1$s`),
                UNIQUE KEY `unique_name` (`name`),
                INDEX `executed_at` (`executed_at`)
            )
            ENGINE = INNODB
            DEFAULT CHARSET = UTF8MB4',
            self::TABLE_NAME
        );

        $this->connection->exec($query);
    }

    /**
     * @throws SchemaUpdateLockException
     *
     * @return bool
     */
    protected function lock(): bool
    {
        $query = sprintf(
            'INSERT INTO `%s` (`name`, `created_at`, `executed_at`) VALUES (\'lock\', NOW(), NULL);',
            self::TABLE_NAME
        );

        $affected = $this->connection->exec($query);

        if ($affected < 1) {
            throw new SchemaUpdateLockException("Could not create lock record (no rows affected)");
        }

        $query = sprintf(
            'SELECT * FROM `%s` WHERE executed_at IS NULL AND `name` != \'lock\'',
            self::TABLE_NAME
        );

        $preparedStatement = $this->connection->query($query, Connection::FETCH_ASSOC);

        if ($preparedStatement->rowCount() > 0) {
            $firstEntry = $preparedStatement->fetch();

            throw new SchemaUpdateLockException("An unfinished schema update was detected. Manual intervention required: ". $firstEntry['name']);
        }

        return true;
    }

    public function unlock(): void
    {
        $query = sprintf(
            'DELETE FROM `%s` WHERE `name` = \'lock\'',
            self::TABLE_NAME
        );

        $this->connection->exec($query);
    }

    /**
     * @throws SchemaUpdateInvalidFormatException
     * @throws DuplicateSchemaUpdateException
     * @throws SchemaUpdateClassNotFoundException
     */
    protected function loadUpdates(): void
    {
        $usedSchemaUpdateClassNames = [];

        $dir = dir($this->updatesPath);
        while (false !== ($schemaUpdateFileName = $dir->read())) {
            if (!str_ends_with($schemaUpdateFileName, '.php')) {
                continue;
            }

            if (!preg_match('/^(?P<date>[0-9]{4}_[0-9]{2}_[0-9]{2}_[0-9]{4})_/', $schemaUpdateFileName, $matches)) {
                throw new SchemaUpdateInvalidFormatException($schemaUpdateFileName);
            }

            $schemaUpdateDatePart = $matches['date'];
            $schemaUpdateDateTime = DateTimeImmutable::createFromFormat('!Y_m_d_Hi', $schemaUpdateDatePart);
            $schemaUpdateClassName = str_replace($schemaUpdateDatePart . '_', '', $schemaUpdateFileName);
            $schemaUpdateClassName = substr($schemaUpdateClassName, 0, -4);

            if (array_key_exists($schemaUpdateClassName, $usedSchemaUpdateClassNames)) {
                $usedSchemaUpdateFileName = $usedSchemaUpdateClassNames[$schemaUpdateClassName];

                throw new DuplicateSchemaUpdateException(
                    $schemaUpdateClassName,
                    $schemaUpdateFileName,
                    $usedSchemaUpdateFileName
                );
            }

            $usedSchemaUpdateClassNames[$schemaUpdateClassName] = $schemaUpdateFileName;

            $fullClassName = '\\'. $schemaUpdateClassName;

            require_once ($this->updatesPath .'/'. $schemaUpdateFileName);

            if (!class_exists($fullClassName)) {
                throw new SchemaUpdateClassNotFoundException($fullClassName, $schemaUpdateFileName);
            }

            $key = $schemaUpdateDateTime->format("YmdHi") . "_". $schemaUpdateClassName;

            $this->updates[$key] = [
                'name' => $schemaUpdateFileName,
                'schemaUpdateClassName' => $schemaUpdateClassName,
            ];
        }

        ksort($this->updates);
    }

    protected function loadExecutedUpdates() : void
    {
        $sql = sprintf(
            'SELECT * FROM `%s` WHERE `executed_at` IS NOT NULL ORDER BY `executed_at` ASC',
            self::TABLE_NAME
        );

        $result = $this->connection->query($sql, Connection::FETCH_ASSOC);
        foreach ($result as $entry) {
            if ($entry['name'] === 'lock') {
                continue;
            }

            $executedAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $entry['executed_at']);
            $key = $executedAt->format('YmdHis') . '_' . $entry['name'];
            $this->executedUpdates[$key] = [
                'name' => $entry['name'],
                'executed_at' => $executedAt
            ];
        }
    }

    protected function selectUpdatesToExecute() : void
    {
        $executedSchemaUpdateNames = [];
        foreach ($this->executedUpdates as $schemaUpdate) {
            $executedSchemaUpdateNames[] = $schemaUpdate['name'];
        }

        foreach ($this->updates as $schemaUpdate) {
            if (in_array($schemaUpdate['name'], $executedSchemaUpdateNames)) {
                continue;
            }

            $this->updatesToExecute[] = $schemaUpdate;
        }
    }

    /**
     * @throws SchemaUpdateClassNotFoundException
     */
    public function executeUpdates() : void
    {
        foreach ($this->updatesToExecute as $key => $schemaUpdate) {
            $this->executeUpdate($schemaUpdate);

            $executedAt = new DateTimeImmutable();
            unset($this->updatesToExecute[$key]);
            $this->executedUpdates[] = [
                'name' => $schemaUpdate['name'],
                'executed_at' => $executedAt,
            ];
        }
    }

    /**
     * @param array $schemaUpdate
     *
     * @throws SchemaUpdateClassNotFoundException
     */
    protected function executeUpdate(array $schemaUpdate): void
    {
        $query = sprintf(
            'INSERT INTO `%s` 
          (`name`, `created_at`, `executed_at`) VALUES
          (:name, NOW(), NULL);',
            self::TABLE_NAME
        );

        $statement = $this->connection->prepare($query);
        $statement->execute(['name' => $schemaUpdate['name']]);

        $schemaUpdateClassName = '\\'. $schemaUpdate['schemaUpdateClassName'];

        require_once ($this->updatesPath .'/'. $schemaUpdate['name']);

        if (!class_exists($schemaUpdateClassName)) {
            throw new SchemaUpdateClassNotFoundException($schemaUpdate['schemaUpdateClassName'], $schemaUpdate['name']);
        }

        /** @var AbstractMigration $seed */
        $seed = new $schemaUpdateClassName($this->connection);
        $seed->up();

        $query = sprintf(
            'UPDATE `%s` SET `executed_at` = NOW() WHERE `name` = :name',
            self::TABLE_NAME
        );

        $statement = $this->connection->prepare($query);
        $statement->execute(['name' => $schemaUpdate['name']]);
    }

    public function getExecutedUpdates(): array
    {
        return $this->executedUpdates;
    }

    public function getUpdatesToExecute(): array
    {
        return $this->updatesToExecute;
    }

    public function getUpdates(): array
    {
        return $this->updates;
    }
}
<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL;

use DateTimeImmutable;
use Exception;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Exception\MigrationException;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Exception\MigrationIntegrityException;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Exception\MigrationLockException;
use UnexpectedValueException;

class MigrationManager
{
    private const MIGRATIONS_TABLE_NAME = 'migration';

    protected Connection $connection;
    protected string $migrationsPath;
    protected array $migrations = [];
    protected array $migrationsToExecute = [];
    protected array $executedMigrations = [];

    public function __construct(Connection $connection, string $migrationsPath)
    {
        $this->connection = $connection;
        $this->migrationsPath = $migrationsPath;
    }

    /**
     * @throws MigrationException
     */
    public function run(): void
    {
        $this->initializeTable();
        $this->lock();

        try {
            $this->loadMigrations();
            $this->loadExecutedMigrations();
            $this->selectMigrationsToExecute();
        } catch (MigrationException $e) {
            $this->unlock();

            throw $e;
        }
    }

    protected function initializeTable(): void
    {
        $query = sprintf(
            "CREATE TABLE IF NOT EXISTS `%s` (
                `id_migration` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NOT NULL,
                `created_at` DATETIME NOT NULL,
                `executed_at` DATETIME NULL DEFAULT NULL,
                PRIMARY KEY (`id_migration`),
                UNIQUE KEY `unique_name` (`name`),
                INDEX `executed_at` (`executed_at`)
            )
            ENGINE = INNODB
            DEFAULT CHARSET = UTF8MB4",
            self::MIGRATIONS_TABLE_NAME
        );

        $this->connection->exec($query);
    }

    /**
     * @throws MigrationLockException
     *
     * @return bool
     */
    protected function lock(): bool
    {
        $query = sprintf(
            "INSERT INTO `%s` (`name`, `created_at`, `executed_at`) VALUES ('lock', NOW(), NULL);",
            self::MIGRATIONS_TABLE_NAME
        );

        try {
            $affected = $this->connection->exec($query);

            if ($affected < 1) {
                throw new MigrationLockException("Could not create lock record (no rows affected)");
            }
        } catch (Exception $e) {
            throw new MigrationLockException("Failed to lock migrations (already running?)", 0, $e);
        }

        $query = sprintf(
            "SELECT * FROM `%s` WHERE executed_at IS NULL AND `name` != 'lock'",
            self::MIGRATIONS_TABLE_NAME
        );

        $preparedStatement = $this->connection->query($query, Connection::FETCH_ASSOC);

        if ($preparedStatement->rowCount() > 0) {
            $firstEntry = $preparedStatement->fetch();

            throw new MigrationLockException("An unfinished migration was detected. Manual intervention required: ". $firstEntry['name']);
        }

        return true;
    }

    public function unlock(): void
    {
        $query = sprintf(
            "DELETE FROM `%s` WHERE `name` = 'lock'",
            self::MIGRATIONS_TABLE_NAME
        );

        $this->connection->exec($query);
    }

    /**
     * @throws MigrationIntegrityException
     */
    protected function loadMigrations(): void
    {
        $usedMigrationClassNames = [];

        $dir = dir($this->migrationsPath);
        while (false !== ($file = $dir->read())) {
            if (!str_ends_with($file, '.php')) {
                continue;
            }

            if (!preg_match('/^(?P<date>[0-9]{4}_[0-9]{2}_[0-9]{2}_[0-9]{4})_/', $file, $matches)) {
                throw new UnexpectedValueException("A migration file uses an invalid filename format: {$file} (missing date part)");
            }

            $migrationDatePart = $matches['date'];
            $migrationDateTime = DateTimeImmutable::createFromFormat('!Y_m_d_Hi', $migrationDatePart);
            $migrationClassName = str_replace($migrationDatePart . '_', '', $file);
            $migrationClassName = substr($migrationClassName, 0, -4);

            if (array_key_exists($migrationClassName, $usedMigrationClassNames)) {
                $dupedIn = $usedMigrationClassNames[$migrationClassName];

                throw new MigrationIntegrityException("Duplicate migration name: {$migrationClassName} (file: {$file}, already declared in: {$dupedIn})");
            }

            $usedMigrationClassNames[$migrationClassName] = $file;

            $fqClassName = '\\'. $migrationClassName;

            require_once ($this->migrationsPath .'/'. $file);

            if (!class_exists($fqClassName)) {

                throw new MigrationIntegrityException("Migration class not found: ". $fqClassName ." (file: ". $file .")");
            }

            $key = $migrationDateTime->format("YmdHi") . "_". $migrationClassName;

            $this->migrations[$key] = [
                'name' => $file,
                'migrationClassName' => $migrationClassName,
            ];
        }

        ksort($this->migrations);
    }

    protected function loadExecutedMigrations() : void
    {
        $sql = sprintf(
            "SELECT * FROM `%s` WHERE executed_at IS NOT NULL ORDER BY executed_at ASC",
            self::MIGRATIONS_TABLE_NAME
        );

        $result = $this->connection->query($sql, Connection::FETCH_ASSOC);
        foreach ($result as $entry) {
            if ($entry['name'] === 'lock') {
                continue;
            }

            // We use the execution date as the key so we list items in the order they were executed
            $executedAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $entry['executed_at']);
            $key = $executedAt->format('YmdHis') .'_'. $entry['name'];
            $this->executedMigrations[$key] = [
                'name' => $entry['name'],
                'executed_at' => $executedAt
            ];
        }
    }

    protected function selectMigrationsToExecute() : void
    {
        $executedMigrationsNames = [];
        foreach ($this->executedMigrations as $migration) {
            $executedMigrationsNames[] = $migration['name'];
        }

        foreach ($this->migrations as $migration) {
            if (in_array($migration['name'], $executedMigrationsNames)) {
                continue;
            }

            $this->migrationsToExecute[] = $migration;
        }
    }

    /**
     * @throws MigrationIntegrityException
     */
    public function executeMigrations() : void
    {
        foreach ($this->migrationsToExecute as $key => $migration) {
            $this->executeMigration($migration);

            $executedAt = new DateTimeImmutable();
            unset($this->migrationsToExecute[$key]);
            $this->executedMigrations[] = [
                'name' => $migration['name'],
                'executed_at' => $executedAt,
            ];
        }
    }

    /**
     * @param array $migrationEntry
     *
     * @throws MigrationIntegrityException
     */
    protected function executeMigration(array $migrationEntry): void
    {
        $query = sprintf(
            "INSERT INTO `%s` 
          (`name`, `created_at`, `executed_at`) VALUES
          (:name, NOW(), NULL);",
            self::MIGRATIONS_TABLE_NAME
        );

        $statement = $this->connection->prepare($query);
        $statement->execute(['name' => $migrationEntry['name']]);

        $migrationClassName = '\\'. $migrationEntry['migrationClassName'];

        require_once ($this->migrationsPath .'/'. $migrationEntry['name']);

        if (!class_exists($migrationClassName)) {
            throw new MigrationIntegrityException("Migration class not found: ". $migrationEntry['migrationClassName'] ." (file: ". $migrationEntry['name'] .")");
        }

        /** @var AbstractMigration $migration */
        $migration = new $migrationClassName($this->connection);
        $migration->up();

        $query = sprintf(
            "UPDATE `%s` SET executed_at = NOW() WHERE `name` = :name",
            self::MIGRATIONS_TABLE_NAME
        );

        $statement = $this->connection->prepare($query);
        $statement->execute(['name' => $migrationEntry['name']]);
    }

    public function getExecutedMigrations(): array
    {
        return $this->executedMigrations;
    }

    public function getPendingMigrations(): array
    {
        return $this->migrationsToExecute;
    }

    public function getMigrations(): array
    {
        return $this->migrations;
    }
}
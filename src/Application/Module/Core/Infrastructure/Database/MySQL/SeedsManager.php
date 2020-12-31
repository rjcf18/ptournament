<?php declare(strict_types=1);
namespace PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL;

use DateTimeImmutable;
use Exception;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Exception\MigrationException;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Exception\MigrationIntegrityException;
use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\Exception\MigrationLockException;
use UnexpectedValueException;

class SeedsManager
{
    private const SEEDS_TABLE_NAME = 'seed';

    protected Connection $connection;
    protected string $seedsPath;
    protected array $seeds = [];
    protected array $seedsToExecute = [];
    protected array $executedSeeds = [];

    public function __construct(Connection $connection, string $seedsPath)
    {
        $this->connection = $connection;
        $this->seedsPath = $seedsPath;
    }

    /**
     * @throws MigrationException
     */
    public function run(): void
    {
        $this->initializeTable();
        $this->lock();

        try {
            $this->loadSeeds();
            $this->loadExecutedSeeds();
            $this->selectSeedsToExecute();
        } catch (MigrationException $e) {
            $this->unlock();

            throw $e;
        }
    }

    protected function initializeTable(): void
    {
        $query = sprintf(
            "CREATE TABLE IF NOT EXISTS `%s` (
                `id_seed` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NOT NULL,
                `created_at` DATETIME NOT NULL,
                `executed_at` DATETIME NULL DEFAULT NULL,
                PRIMARY KEY (`id_seed`),
                UNIQUE KEY `unique_name` (`name`),
                INDEX `executed_at` (`executed_at`)
            )
            ENGINE = INNODB
            DEFAULT CHARSET = UTF8MB4",
            self::SEEDS_TABLE_NAME
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
            self::SEEDS_TABLE_NAME
        );

        try {
            $affected = $this->connection->exec($query);

            if ($affected < 1) {
                throw new MigrationLockException("Could not create lock record (no rows affected)");
            }
        } catch (Exception $e) {
            throw new MigrationLockException("Failed to lock seeds (already running?)", 0, $e);
        }

        $query = sprintf(
            "SELECT * FROM `%s` WHERE executed_at IS NULL AND `name` != 'lock'",
            self::SEEDS_TABLE_NAME
        );

        $preparedStatement = $this->connection->query($query, Connection::FETCH_ASSOC);

        if ($preparedStatement->rowCount() > 0) {
            $firstEntry = $preparedStatement->fetch();

            throw new MigrationLockException("An unfinished seed was detected. Manual intervention required: ". $firstEntry['name']);
        }

        return true;
    }

    public function unlock(): void
    {
        $query = sprintf(
            "DELETE FROM `%s` WHERE `name` = 'lock'",
            self::SEEDS_TABLE_NAME
        );

        $this->connection->exec($query);
    }

    /**
     * @throws MigrationIntegrityException
     */
    protected function loadSeeds(): void
    {
        $usedMigrationClassNames = [];

        $dir = dir($this->seedsPath);
        while (false !== ($file = $dir->read())) {
            if (!str_ends_with($file, '.php')) {
                continue;
            }

            if (!preg_match('/^(?P<date>[0-9]{4}_[0-9]{2}_[0-9]{2}_[0-9]{4})_/', $file, $matches)) {
                throw new UnexpectedValueException("A seed file uses an invalid filename format: {$file} (missing date part)");
            }

            $migrationDatePart = $matches['date'];
            $migrationDateTime = DateTimeImmutable::createFromFormat('!Y_m_d_Hi', $migrationDatePart);
            $seedClassName = str_replace($migrationDatePart . '_', '', $file);
            $seedClassName = substr($seedClassName, 0, -4);

            if (array_key_exists($seedClassName, $usedMigrationClassNames)) {
                $dupedIn = $usedMigrationClassNames[$seedClassName];

                throw new MigrationIntegrityException("Duplicate seed name: {$seedClassName} (file: {$file}, already declared in: {$dupedIn})");
            }

            $usedMigrationClassNames[$seedClassName] = $file;

            $fqClassName = '\\'. $seedClassName;

            require_once ($this->seedsPath .'/'. $file);

            if (!class_exists($fqClassName)) {

                throw new MigrationIntegrityException("Seed class not found: ". $fqClassName ." (file: ". $file .")");
            }

            $key = $migrationDateTime->format("YmdHi") . "_". $seedClassName;

            $this->seeds[$key] = [
                'name' => $file,
                'seedClassName' => $seedClassName,
            ];
        }

        ksort($this->seeds);
    }

    protected function loadExecutedSeeds() : void
    {
        $sql = sprintf(
            "SELECT * FROM `%s` WHERE executed_at IS NOT NULL ORDER BY executed_at ASC",
            self::SEEDS_TABLE_NAME
        );

        $result = $this->connection->query($sql, Connection::FETCH_ASSOC);
        foreach ($result as $entry) {
            if ($entry['name'] === 'lock') {
                continue;
            }

            // We use the execution date as the key so we list items in the order they were executed
            $executedAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $entry['executed_at']);
            $key = $executedAt->format('YmdHis') .'_'. $entry['name'];
            $this->executedSeeds[$key] = [
                'name' => $entry['name'],
                'executed_at' => $executedAt
            ];
        }
    }

    protected function selectSeedsToExecute() : void
    {
        $executedSeedsNames = [];
        foreach ($this->executedSeeds as $seed) {
            $executedSeedsNames[] = $seed['name'];
        }

        foreach ($this->seeds as $seed) {
            if (in_array($seed['name'], $executedSeedsNames)) {
                continue;
            }

            $this->seedsToExecute[] = $seed;
        }
    }

    /**
     * @throws MigrationIntegrityException
     */
    public function executeSeeds() : void
    {
        foreach ($this->seedsToExecute as $key => $seed) {
            $this->executeSeed($seed);

            $executedAt = new DateTimeImmutable();
            unset($this->seedsToExecute[$key]);
            $this->executedSeeds[] = [
                'name' => $seed['name'],
                'executed_at' => $executedAt,
            ];
        }
    }

    /**
     * @param array $seedEntry
     *
     * @throws MigrationIntegrityException
     */
    protected function executeSeed(array $seedEntry): void
    {
        $query = sprintf(
            "INSERT INTO `%s` 
          (`name`, `created_at`, `executed_at`) VALUES
          (:name, NOW(), NULL);",
            self::SEEDS_TABLE_NAME
        );

        $statement = $this->connection->prepare($query);
        $statement->execute(['name' => $seedEntry['name']]);

        $seedClassName = '\\'. $seedEntry['seedClassName'];

        require_once ($this->seedsPath .'/'. $seedEntry['name']);

        if (!class_exists($seedClassName)) {
            throw new MigrationIntegrityException("Seed class not found: ". $seedEntry['seedClassName'] ." (file: ". $seedEntry['name'] .")");
        }

        /** @var AbstractMigration $seed */
        $seed = new $seedClassName($this->connection);
        $seed->up();

        $query = sprintf(
            "UPDATE `%s` SET executed_at = NOW() WHERE `name` = :name",
            self::SEEDS_TABLE_NAME
        );

        $statement = $this->connection->prepare($query);
        $statement->execute(['name' => $seedEntry['name']]);
    }

    public function getExecutedSeeds(): array
    {
        return $this->executedSeeds;
    }

    public function getPendingMigrations(): array
    {
        return $this->seedsToExecute;
    }

    public function getSeeds(): array
    {
        return $this->seeds;
    }
}
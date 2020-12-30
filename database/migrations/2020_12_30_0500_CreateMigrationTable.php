<?php declare(strict_types=1);

use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\AbstractMigration;

class CreateMigrationTable extends AbstractMigration
{
    private const TABLE_NAME = 'migration';

    public function up(): void
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
            self::TABLE_NAME
        );

        $this->connection->exec($query);
    }
}
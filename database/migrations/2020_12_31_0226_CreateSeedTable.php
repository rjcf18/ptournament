<?php declare(strict_types=1);

use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\AbstractMigration;

class CreateSeedTable extends AbstractMigration
{
    private const TABLE_NAME = 'seed';

    public function up(): void
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
            self::TABLE_NAME
        );

        $this->connection->exec($query);
    }
}
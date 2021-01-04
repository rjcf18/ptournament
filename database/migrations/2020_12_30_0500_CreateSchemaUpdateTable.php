<?php declare(strict_types=1);

use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\AbstractMigration;

class CreateSchemaUpdateTable extends AbstractMigration
{
    private const TABLE_NAME = 'schema_update';

    public function up(): void
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
}
<?php declare(strict_types=1);

use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\AbstractMigration;

class CreateFriendTable extends AbstractMigration
{
    private const TABLE_NAME = 'friend';

    public function up(): void
    {
        $query = sprintf(
            "CREATE TABLE IF NOT EXISTS `%s` (
                `id_friend` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NOT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                PRIMARY KEY (`id_friend`)
            )
            ENGINE = INNODB
            DEFAULT CHARSET = UTF8MB4",
            self::TABLE_NAME
        );

        $this->connection->exec($query);
    }
}
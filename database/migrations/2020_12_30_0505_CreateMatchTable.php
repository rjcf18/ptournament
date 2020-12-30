<?php declare(strict_types=1);

use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\AbstractMigration;

class CreateMatchTable extends AbstractMigration
{
    private const TABLE_NAME = 'match';

    public function up(): void
    {
        $query = sprintf(
            "CREATE TABLE IF NOT EXISTS `%s` (
                `id_match` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `looser_balls_left` INT NOT NULL,
                `winner` INT UNSIGNED NULL DEFAULT NULL,
                `match_date` DATETIME NOT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                PRIMARY KEY (`id_match`),
                CONSTRAINT `match_winner_friend_fk` FOREIGN KEY (`winner`) REFERENCES `friend` (`id_friend`) ON DELETE CASCADE ON UPDATE CASCADE
            )
            ENGINE = INNODB
            DEFAULT CHARSET = UTF8MB4",
            self::TABLE_NAME
        );

        $this->connection->exec($query);
    }
}
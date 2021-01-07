<?php declare(strict_types=1);

use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\AbstractMigration;

class AddMatchLooserField extends AbstractMigration
{
    private const TABLE_NAME = 'match';

    public function up(): void
    {
        $query = sprintf(
            'ALTER TABLE `%1$s`
                ADD COLUMN `looser_id` INT UNSIGNED NOT NULL AFTER `winner_id`,
                ADD CONSTRAINT `match_looser_friend_fk`
                    FOREIGN KEY (`looser_id`) REFERENCES `friend` (`id_friend`) ON DELETE CASCADE ON UPDATE CASCADE;',
            self::TABLE_NAME
        );

        $this->connection->exec($query);
    }
}
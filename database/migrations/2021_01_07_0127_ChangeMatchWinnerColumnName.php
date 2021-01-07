<?php declare(strict_types=1);

use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\AbstractMigration;

class ChangeMatchWinnerColumnName extends AbstractMigration
{
    private const TABLE_NAME = 'match';

    public function up(): void
    {
        $query = sprintf(
            'ALTER TABLE `%1$s`
                CHANGE COLUMN `winner` `winner_id` INT UNSIGNED NOT NULL,
                ALGORITHM=INPLACE, LOCK=NONE;',
            self::TABLE_NAME
        );

        $this->connection->exec($query);
    }
}
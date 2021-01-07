<?php declare(strict_types=1);

use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\AbstractMigration;

class AddFriendPoints extends AbstractMigration
{
    private const TABLE_NAME = 'friend';

    public function up(): void
    {
        $query = sprintf(
            'ALTER TABLE `%1$s`
                ADD COLUMN `points` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `name`,
                ALGORITHM=INPLACE, LOCK=NONE;',
            self::TABLE_NAME
        );

        $this->connection->exec($query);
    }
}
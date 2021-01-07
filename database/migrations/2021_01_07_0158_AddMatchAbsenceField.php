<?php declare(strict_types=1);

use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\AbstractMigration;

class AddMatchAbsenceField extends AbstractMigration
{
    private const TABLE_NAME = 'match';

    public function up(): void
    {
        $query = sprintf(
            'ALTER TABLE `%1$s`
                ADD COLUMN `absence` TINYINT(1) NOT NULL AFTER `looser_id`,
                ALGORITHM=INPLACE, LOCK=NONE;',
            self::TABLE_NAME
        );

        $this->connection->exec($query);
    }
}
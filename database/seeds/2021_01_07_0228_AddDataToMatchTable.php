<?php declare(strict_types=1);

use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\AbstractMigration;

class AddDataToMatchTable extends AbstractMigration
{
    private const TABLE_NAME = 'match';

    public function up(): void
    {
        $query = sprintf(
            "INSERT INTO `%s` (`winner_id`, `looser_id`, `looser_balls_left`, `absence`, `date`, `created_at`, `updated_at`) 
                    VALUES
                        (1, 2, 8, 0, DATE_SUB(NOW(), INTERVAL 30 MINUTE), NOW(), NOW()),
                        (3, 4, 7, 0, DATE_SUB(NOW(), INTERVAL 25 MINUTE), NOW(), NOW()),
                        (1, 3, 9, 1, DATE_SUB(NOW(), INTERVAL 22 MINUTE), NOW(), NOW()),
                        (2, 4, 9, 1, DATE_SUB(NOW(), INTERVAL 15 MINUTE), NOW(), NOW()),
                        (2, 3, 3, 0, DATE_SUB(NOW(), INTERVAL 12 MINUTE), NOW(), NOW()),
                        (5, 3, 1, 0, DATE_SUB(NOW(), INTERVAL 10 MINUTE), NOW(), NOW());",
            self::TABLE_NAME
        );

        $this->connection->exec($query);
    }
}
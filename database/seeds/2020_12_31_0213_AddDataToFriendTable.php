<?php declare(strict_types=1);

use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\AbstractMigration;

class AddDataToFriendTable extends AbstractMigration
{
    private const TABLE_NAME = 'friend';

    public function up(): void
    {
        $query = sprintf(
            "INSERT INTO `%s` (`name`, `points`, `balls`, `created_at`, `updated_at`) 
                    VALUES
                        ('Friend A', 6, 0, NOW(), NOW()),
                        ('Friend B', 1, 8, NOW(), NOW()),
                        ('Friend C', 2, 13, NOW(), NOW()),
                        ('Friend D', 1, 16, NOW(), NOW()),
                        ('Friend E', 3, 0, NOW(), NOW()),
                        ('Friend F', 0, 0, NOW(), NOW()),
                        ('Friend G', 0, 0, NOW(), NOW()),
                        ('Friend H', 0, 0, NOW(), NOW()),
                        ('Friend I', 0, 0, NOW(), NOW()),
                        ('Friend J', 0, 0, NOW(), NOW());",
            self::TABLE_NAME
        );

        $this->connection->exec($query);
    }
}
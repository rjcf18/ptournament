<?php declare(strict_types=1);

use PoolTournament\Application\Module\Core\Infrastructure\Database\MySQL\AbstractMigration;

class AddDataToFriendTable extends AbstractMigration
{
    private const TABLE_NAME = 'friend';

    public function up(): void
    {
        $query = sprintf(
            "INSERT INTO `%s` (`name`, `created_at`, `updated_at`) 
                    VALUES
                        ('Friend A', NOW(), NOW()),
                        ('Friend B', NOW(), NOW()),
                        ('Friend C', NOW(), NOW()),
                        ('Friend D', NOW(), NOW()),
                        ('Friend E', NOW(), NOW()),
                        ('Friend F', NOW(), NOW());",
            self::TABLE_NAME
        );

        $this->connection->exec($query);
    }
}
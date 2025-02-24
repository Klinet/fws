<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;

class DatabaseTest extends TestCase
{
    private $connection;

    protected function setUp(): void
    {
        $connectionParams = [
            'dbname'   => $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE'),
            'user'     => $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME'),
            'password' => $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD'),
            'host'     => $_ENV['DB_HOST'] ?? getenv('DB_HOST'),
            'driver'   => 'pdo_mysql',
        ];

        try {
            $this->connection = DriverManager::getConnection($connectionParams);
        } catch (Exception $e) {
            $this->fail("Database connection failed: " . $e->getMessage());
        }
    }

    public function testDatabaseConnection()
    {
        $this->assertNotNull($this->connection, "Database connection should be established.");
    }
}
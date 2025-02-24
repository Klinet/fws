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
        // Load database connection parameters from environment variables
        $connectionParams = [
            'dbname'   => 'fws-db-dev',
            'user'     => 'root',
            'password' => 'root',
            'host'     => 'mysql_db',
            'driver'   => 'pdo_mysql',
        ];

        try {
            // Establish the database connection
            $this->connection = DriverManager::getConnection($connectionParams);
        } catch (Exception $e) {
            $this->fail("Database connection failed: " . $e->getMessage());
        }
    }

    protected function tearDown(): void
    {
        // Clean up the database after each test
        if ($this->connection) {
            $this->connection->executeStatement("DELETE FROM products WHERE title = 'Test Product'");
        }
    }

    public function testDatabaseExists()
    {
        $this->assertNotNull($this->connection, "Database connection should not be null.");
    }

    public function testCanRetrieveTables()
    {
        $stmt = $this->connection->query("SHOW TABLES;");
        $tables = $stmt->fetchAllAssociative();

        $this->assertNotEmpty($tables, "Database has no tables.");
    }

    public function testCanInsertAndRetrieveData()
    {
        // Insert test data
        $this->connection->executeStatement("INSERT INTO products (title) VALUES ('Test Product')");

        // Retrieve the inserted data
        $stmt = $this->connection->executeQuery("SELECT * FROM products WHERE title = 'Test Product'");
        $result = $stmt->fetchAssociative();

        // Assertions
        $this->assertNotEmpty($result, "Inserted data could not be retrieved.");
        $this->assertEquals('Test Product', $result['title'], "Inserted title does not match expected value.");
    }
}
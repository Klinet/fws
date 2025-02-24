<?php
//todo redundant this only for testing

use Doctrine\DBAL\DriverManager;
use Dotenv\Dotenv;
use Application\Services\ProductImportService;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$connectionParams = [
    'dbname'   => $_ENV['DB_DATABASE'],
    'user'     => $_ENV['DB_USERNAME'],
    'password' => $_ENV['DB_PASSWORD'],
    'host'     => $_ENV['DB_HOST'],
    'driver'   => 'pdo_mysql',
];

try {
    $db = DriverManager::getConnection($connectionParams);
} catch (\Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

$productImportService = new ProductImportService($db);
return $productImportService;


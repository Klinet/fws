<?php

use Application\Commands\ImportProductsCommand;
use Application\Commands\GenerateXmlCommand;
use Application\Services\ProductImportService;
use Application\Services\ProductExportService;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Console\Application;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
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
$productExportService = new ProductExportService($db);

$console = new Application();

// Register the commands
$console->add(new ImportProductsCommand($productImportService));
$console->add(new GenerateXmlCommand($productExportService));

$input = new Symfony\Component\Console\Input\ArrayInput(['command' => 'app:import-products']);
$console->find('app:import-products')->run($input, new Symfony\Component\Console\Output\ConsoleOutput());

$input = new Symfony\Component\Console\Input\ArrayInput(['command' => 'app:generate-xml']);
$console->find('app:generate-xml')->run($input, new Symfony\Component\Console\Output\ConsoleOutput());

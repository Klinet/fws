<?php

namespace Application\Commands;

use Application\Services\ProductImportService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportProductsCommand extends Command
{
    protected static $defaultName = 'app:import-products';  // <-- This line must be present

    private ProductImportService $importService;

    public function __construct(ProductImportService $importService)
    {
        parent::__construct();
        $this->setName('app:import-products')
            ->setDescription('CSV importálása');
        $this->importService = $importService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = __DIR__ . '/../../../termekek.csv';

        try {
            $this->importService->importCsv($filePath);
            $output->writeln('<info>Products imported successfully!</info>');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>Error importing products: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
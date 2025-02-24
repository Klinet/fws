<?php

namespace Application\Commands;

use Application\Services\ProductExportService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateXmlCommand extends Command
{
    protected static $defaultName = 'app:generate-xml';

    private ProductExportService $exportService;

    public function __construct(ProductExportService $exportService)
    {
        parent::__construct();
        $this->setName('app:generate-xml')
            ->setDescription('Termékfeed (XML) generálása');
        $this->exportService = $exportService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            // Generate the XML feed
            $xmlContent = $this->exportService->generateXmlFeed();

            // Save to a file (or you can output it in another format if needed)
            file_put_contents('product_feed.xml', $xmlContent);

            $output->writeln('<info>XML feed generated successfully!</info>');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>Error generating XML feed: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
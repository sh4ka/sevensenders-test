<?php

namespace App\Command;

use App\Command\Manager\FileManager;
use App\Command\Parsers\ParserFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProcessCommand extends Command
{

    protected function configure()
    {
        $this->setName('app:process')
            ->setDescription('Process input data and creates output ready to use by the api')
            ->setHelp('This command takes relative paths for input and output')
            ->addArgument(
                'input-location',
                InputArgument::OPTIONAL,
                'Input location (full path)',
                '../input'
            )
            ->addArgument(
                'output-location',
                InputArgument::OPTIONAL,
                'Input location (full path)',
                '../output'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sfStyle = new SymfonyStyle($input, $output);
        $sfStyle->title('Starting file processing');

        // load input files, for this test we expect one .xml and one .json
        // we will combine this information into a single json file
        $inputLocation = $input->getArgument('input-location');
        $outputLocation = $input->getArgument('output-location');

        $fileManager = new FileManager($inputLocation, $outputLocation);
        if (!$fileManager->areLocationsValid()) {
            $sfStyle->error('Provided locations are invalid');
            return Command::FAILURE;
        }
        $inputFiles = $fileManager->getFilesInInput();
        if (empty($inputFiles)) {
            $sfStyle->warning('No files to process');
            return Command::SUCCESS;
        }

        $sfStyle->write('Detected files');
        $sfStyle->listing($inputFiles);

        $xmlFile = '';
        $jsonFile = '';
        foreach ($inputFiles as $inputFile) {
            if ($fileManager->getExtension($inputFile) === 'json') {
                $jsonFile = $inputFile;
            }
            if ($fileManager->getExtension($inputFile) === 'xml') {
                $xmlFile = $inputFile;
            }
        }

        $output = [
            'products' => []
        ];

        if (!empty($xmlFile) && !empty($jsonFile)) {
            $sfStyle->comment('Processing Xml');
            // parse product xml
            $parser = ParserFactory::getParserInstance('xml');
            foreach ($parser->parse($fileManager->getInputFile($xmlFile)) as $node) {
                $output['products'][$node['sku']] = $node;
            }

            $sfStyle->comment('Processing Json');
            // parse json
            $parser = ParserFactory::getParserInstance('json');
            foreach ($parser->parse($fileManager->getInputFile($jsonFile)) as $node) {
                $output['products'][$node['id']]['prices'][$node['unit']] = $node;
            }
        }

        $fileManager->writeOutputFile($output);

        $sfStyle->success('Process finished');

        return Command::SUCCESS;
    }
}
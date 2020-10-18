<?php

namespace App\Command;

use App\Service\MediaHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class DownloadDataCommand extends Command
{
    /**
     * @var MediaHandler
     */
    private MediaHandler $mediaStorage;

    /**
     * Command constructor.
     *
     * @param MediaHandler $mediaStorage
     */
    public function __construct(MediaHandler $mediaStorage)
    {
        $this->mediaStorage = $mediaStorage;

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('mg:download')
            ->setDescription('Retrieve and store the external data.');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $console = new SymfonyStyle($input, $output);

        $console->title('MG Downloader');

        $console->section('Starting download process.');

        try {
            $this->mediaStorage->processData($console);
        } catch (Throwable $exception) {
            $console->error(['An error has occurred during the downloading process.', $exception->getMessage()]);
        }

        $console->success('Data has been successfully downloaded.');
    }
}

<?php

namespace App\Command;

use App\Service\Downloader;
use App\Service\MediaStorage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildDataCommand extends Command
{
    /**
     * @var Downloader
     */
    private Downloader $downloader;

    /**
     * @var MediaStorage
     */
    private MediaStorage $mediaStorage;

    public function __construct(Downloader $downloader, MediaStorage $mediaStorage)
    {
        $this->downloader = $downloader;
        $this->mediaStorage = $mediaStorage;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('mg:download')
            ->setDescription('Download and store external data.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //@Todo: Expand into customized console.
        $data = $this->downloader->fetchData();

        $this->mediaStorage->processData($data);
    }
}

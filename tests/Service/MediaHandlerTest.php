<?php

namespace App\Tests\Service;

use App\Entity\Gallery;
use App\Service\Downloader;
use App\Service\MediaHandler;
use App\Tests\TestEngine;
use ReflectionMethod;
use Sonata\MediaBundle\Extra\ApiMediaFile;

class MediaHandlerTest extends TestEngine
{
    /**
     * @var MediaHandler
     */
    private $mediaHandlerService;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mediaHandlerService = self::$container->get(MediaHandler::class);
    }

    /**
     * Test that the media handler temporary file generator outputs a valid file object if given a proper source.
     */
    public function testMediaHandlerTemporaryFileGenerator()
    {
        $method = new ReflectionMethod(MediaHandler::class, '_generateTemporaryFile');
        $method->setAccessible(true);

        $url = 'https://mgtechtest.blob.core.windows.net/images/unscaled/2012/11/29/Parental-Guidance-VPA.jpg';
        $data = $method->invokeArgs($this->mediaHandlerService, [$url]);

        $this->assertInstanceOf(ApiMediaFile::class, $data);
    }

    /**
     * Test that the media handler data processor will output an error if the provided JSON is incomplete.
     */
    public function testMediaHandlerInvalidDataProcessing()
    {
        $data = $this->getDecodedTestFileContents('testIncompleteNoBody.json');

        $mockedDownloader = $this->createMock(Downloader::class);
        $mockedDownloader
            ->expects($this->once())
            ->method('fetchData')
            ->willReturn($data);
        $this->mediaHandlerService->setDownloader($mockedDownloader);

        $this->expectError();
        $this->mediaHandlerService->processData($this->getTestConsoleOutput());
    }

    /**
     * Test that the media handler data processor outputs a Gallery object with the required fields set.
     */
    public function testMediaHandlerDataProcessing()
    {
        $data = $this->getDecodedTestFileContents('test.json');

        $mockedDownloader = $this->createMock(Downloader::class);
        $mockedDownloader
            ->expects($this->once())
            ->method('fetchData')
            ->willReturn($data);
        $this->mediaHandlerService->setDownloader($mockedDownloader);

        $this->mediaHandlerService->processData($this->getTestConsoleOutput());

        $data = $this->mediaHandlerService->getGallery();

        $this->assertInstanceOf(Gallery::class, $data, 'Warning: Object provided is not of expected type.');
        $this->assertObjectHasAttribute('rid', $data, 'Warning: Object missing RID.');
        $this->assertObjectHasAttribute('body', $data, 'Warning: Object missing Body.');
        $this->assertObjectHasAttribute('sum', $data, 'Warning: Object missing SUM.');
    }
}

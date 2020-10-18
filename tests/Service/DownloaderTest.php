<?php

namespace App\Tests\Service;

use App\Service\Downloader;
use App\Tests\TestEngine;
use ReflectionMethod;
use Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Symfony\Component\HttpClient\Exception\JsonException;

class DownloaderTest extends TestEngine
{
    /**
     * @var Downloader
     */
    private $downloaderService;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->downloaderService = self::$container->get(Downloader::class);
    }

    /**
     * Test that the downloader data fetching will output an error if the endpoint is an invalid URL.
     */
    public function testDownloaderInvalidEndpoint()
    {
        $this->downloaderService->setEndpoint('invalid');

        $this->expectException(InvalidArgumentException::class);

        $this->downloaderService->fetchData();
    }

    /**
     * Test that the downloader file content retrieval will output an error if the endpoint is an invalid URL.
     */
    public function testDownloaderFileContentsInvalidEndpoint()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->downloaderService->getFileContents('invalid');
    }

    /**
     * Test that the downloader file content retrieval returns expected output if provided with a valid source.
     */
    public function testDownloaderFileContents()
    {
        $url = 'https://mgtechtest.blob.core.windows.net/images/unscaled/2012/11/29/Parental-Guidance-VPA.jpg';
        $data = $this->downloaderService->getFileContents($url);

        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('content-type', $data);
        $this->assertArrayHasKey('content', $data);
    }

    /**
     * Test that the downloader JSON converter will output an error if it does not receive valid content.
     */
    public function testDownloaderJsonConversionEmptyContent()
    {
        $method = new ReflectionMethod(Downloader::class, '_convertData');
        $method->setAccessible(true);

        $this->expectException(JsonException::class);

        $method->invokeArgs($this->downloaderService, ['', []]);
    }

    /**
     * Test that the downloader JSON converter will output an error if it does not receive a valid header.
     */
    public function testDownloaderJsonConversionInvalidHeader()
    {
        $method = new ReflectionMethod(Downloader::class, '_convertData');
        $method->setAccessible(true);

        $this->expectException(JsonException::class);

        $method->invokeArgs($this->downloaderService, ['content', []]);
    }

    /**
     * Test that the downloader JSON converter will output an error if it receives a valid header but invalid content.
     */
    public function testDownloaderJsonConversionInvalidJson()
    {
        $method = new ReflectionMethod(Downloader::class, '_convertData');
        $method->setAccessible(true);

        $this->expectException(JsonException::class);

        $method->invokeArgs($this->downloaderService, ['content', ['content-type' => 'application/json']]);
    }
}

<?php

namespace App\Service;

use Symfony\Component\HttpClient\Exception\JsonException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Downloader
{
    private string $endpoint = 'https://mgtechtest.blob.core.windows.net/files/showcase.json';

    /**
     * @var HttpClientInterface
     */
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        // @Todo: Use CacheHttpClient
        $this->client = $client;
    }

    public function fetchData()
    {
        $response = $this->client->request('GET', $this->endpoint);

        $content = $response->getContent();

        return $this->convertData($content, $response->getHeaders());
    }

    /**
     * @param string $content
     * @param array $headers
     *
     * @return array
     *
     * @throws JsonException
     */
    protected function convertData(string $content, array $headers) : array
    {
        if (empty($content) || is_null($content)) {
            throw new JsonException('Response body is empty.');
        }

        $contentType = $headers['content-type'][0] ?? 'application/json';

        if (!preg_match('/\bjson\b/i', $contentType)) {
            throw new JsonException(sprintf('Response content-type is "%s" while a JSON-compatible one was expected for "%s".', $contentType, $this->endpoint));
        }

        // @Todo: Catch UTF8 error and output without breaking the system.
        try {
            $content = json_decode($content, true, 512, \JSON_INVALID_UTF8_IGNORE | \JSON_BIGINT_AS_STRING | \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new JsonException($e->getMessage().sprintf(' for "%s".', $this->endpoint), $e->getCode());
        }

        if (!\is_array($content)) {
            throw new JsonException(sprintf('JSON content was expected to decode to an array, "%s" returned for "%s".', \gettype($content), $this->endpoint));
        }

        return $content;
    }
}

<?php

namespace App\Service;

use Symfony\Component\HttpClient\Exception\JsonException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Downloader
{
    private string $endpoint = 'https://mgtechtest.blob.core.windows.net/files/showcase.json';

    /**
     * @var HttpClientInterface
     */
    private HttpClientInterface $client;

    /**
     * Downloader constructor.
     *
     * @param HttpClientInterface $client
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Fetch the data from the endpoint and convert it to array format.
     *
     * @return array
     *   The data to process in array format.
     *
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function fetchData(): array
    {
        $response = $this->client->request('GET', $this->endpoint);

        $content = $response->getContent();

        return $this->_convertData($content, $response->getHeaders());
    }

    /**
     * Create a request and return all contents from the given URL.
     *
     * @param string $url
     *   The targeted URL from which to retrieve data.
     *
     * @return array
     *   An array containing the content and content-type, or empty otherwise the request could not be processed.
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getFileContents(string $url): array
    {
        $request = $this->client->request('GET', $url);

        $contents = [];
        if ($request->getStatusCode() === Response::HTTP_OK) {
            if (isset($request->getHeaders()['content-type'])) {
                $contents = [
                    'content-type' => current($request->getHeaders()['content-type']),
                    'content' => $request->getContent()
                ];
            }
        }

        return $contents;
    }

    /**
     * Helper method to decode the given JSON-compatible data.
     *
     * @param string $content
     *   The content string.
     * @param array $headers
     *   The response headers used to identify content-type.
     *
     * @return array
     *   The decoded content.
     *
     * @throws JsonException
     */
    private function _convertData(string $content, array $headers): array
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

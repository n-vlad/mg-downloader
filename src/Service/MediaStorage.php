<?php

namespace App\Service;

use App\Entity\Cast;
use App\Entity\Directors;
use App\Entity\Gallery;
use App\Entity\GalleryItem;
use App\Entity\Genre;
use App\Entity\Media;
use App\Entity\Video;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\MediaBundle\Extra\ApiMediaFile;
use Sonata\MediaBundle\Model\MediaManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MediaStorage
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var Gallery
     */
    private Gallery $gallery;

    /**
     * @var MediaManagerInterface
     */
    private MediaManagerInterface $mediaManager;

    /**
     * @var HttpClientInterface
     */
    private HttpClientInterface $client;

    public function __construct(MediaManagerInterface $mediaManager, HttpClientInterface $client, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->mediaManager = $mediaManager;
        $this->client = $client;
    }

    /**
     * @param $data
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function processData(array $data)
    {
        foreach ($data as $datum) {
            $this->processGallery($datum);

            $this->processImages($datum['cardImages']);

            $this->processImages($datum['keyArtImages']);
        }
    }

    /**
     * @param array $data
     */
    protected function processGallery(array $data)
    {
        // Due to the uncertain status of the data we are forced to verify each existing key before usage.
        // @Todo: Add remaining fields
        // @Todo: Validate headline and body minimum requirement.
        $this->gallery = new Gallery();
        $this->gallery
            ->setContext('default')
            ->setName($data['headline'])
            ->setBody($data['body'])
            ->setEnabled(true);

        // @Todo: Validate complete structural integrity.
        if (isset($data['genres'])) {
            $this->processGenre($data['genres']);
        }

        // @Todo: Validate complete structural integrity.
        if (isset($data['cast'])) {
            $this->processCast($data['cast']);
        }

        // @Todo: Validate complete structural integrity.
        if (isset($data['directors'])) {
            $this->processDirectors($data['directors']);
        }

        if (isset($data['id'])) {
            $this->gallery->setRid($data['id']);
        }

        if (isset($data['synopsis'])) {
            $this->gallery->setSynopsis($data['synopsis']);
        }

        if (isset($data['class'])) {
            $this->gallery->setClassType($data['class']);
        }

        if (isset($data['cert'])) {
            $this->gallery->setCert($data['cert']);
        }

        if (isset($data['year'])) {
            $this->gallery->setYear($data['year']);
        }

        if (isset($data['sum'])) {
            $this->gallery->setSum($data['sum']);
        }

        if (isset($data['url'])) {
            $this->gallery->setUrl($data['url']);
        }

        if (isset($data['rating'])) {
            $this->gallery->setRating($data['rating']);
        }

        if (isset($data['quote'])) {
            $this->gallery->setQuote($data['quote']);
        }

        if (isset($data['duration'])) {
            $this->gallery->setDuration($data['duration']);
        }

        if (isset($data['reviewAuthor'])) {
            $this->gallery->setReviewAuthor($data['reviewAuthor']);
        }

        if (isset($data['lastUpdated'])) {
            $this->gallery->setLastUpdated($data['lastUpdated']);
        }

        if (isset($data['viewingWindow'])) {
            $this->gallery->setViewingWindow($data['viewingWindow']);
        }

        if (isset($data['skyGoId']) && isset($data['skyGoUrl'])) {
            $this->gallery
                ->setSkyGoId($data['skyGoId'])
                ->setSkyGoUrl($data['skyGoUrl']);
        }

        if (isset($data['videos'])) {
            $this->processVideos($data['videos']);
        }
    }

    /**
     * @param array $images
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function processImages(array $images)
    {
        foreach ($images as $image) {
            $tmpFile = $this->generateTemporaryFile($image['url']);

            if ($tmpFile === FALSE) {
                // @Todo: Log and display issues.
                continue;
            }

            /** @var Media $media */
            $media = $this->mediaManager->create();
            $media->setProviderName('sonata.media.provider.image');
            $media->setBinaryContent($tmpFile);
            $media->setContext('default');
            $media->setHeight($image['h']);
            $media->setWidth($image['w']);
            $media->setName(basename($image['url']));
            $media->setEnabled(true);

            $this->mediaManager->save($media);

            $galleryItem = new GalleryItem();
            $galleryItem->setGallery($this->gallery);
            $galleryItem->setMedia($media);
            $galleryItem->setEnabled(true);

            $this->entityManager->persist($galleryItem);
            $this->entityManager->flush();
        }
    }

    /**
     * @param array $genres
     */
    protected function processGenre(array $genres)
    {
        $processedGenre = new ArrayCollection();
        foreach ($genres as $key => $genre) {
            $entry = $this->entityManager
                ->getRepository(Genre::class)
                ->findOneBy(['name' => $genre]);

            if (is_null($entry)) {
                $entry = new Genre();
                $entry->setName($genre);
                $this->entityManager->persist($entry);
            }

            $processedGenre->set($key, $entry);
        }

        $this->entityManager->flush();

        $this->gallery->setGenre($processedGenre);
    }

    /**
     * @param array $videos
     */
    protected function processVideos(array $videos)
    {
        foreach ($videos as $key => $video) {
            $entry = new Video();
            $entry
                ->setName($video['title'])
                ->setType($video['type'])
                ->setUrl($video['url'])
                ->setGallery($this->gallery);

            if (isset($video['alternatives'])) {
                $entry->setAlternatives($video['alternatives']);
            }

            // @Todo: Change persistence method.
            $this->entityManager->persist($entry);
        }

        $this->entityManager->flush();
    }

    /**
     * @param array $cast
     */
    protected function processCast(array $cast)
    {
        $processedCast = new ArrayCollection();
        foreach ($cast as $key => $castMember) {
            $entry = $this->entityManager
                ->getRepository(Cast::class)
                ->findOneBy(['name' => $castMember['name']]);

            if (is_null($entry)) {
                $entry = new Cast();
                $entry->setName($castMember['name']);
                $this->entityManager->persist($entry);
            }

            $processedCast->set($key, $entry);
        }

        $this->entityManager->flush();

        $this->gallery->setCast($processedCast);
    }

    /**
     * @param array $directors
     */
    protected function processDirectors(array $directors)
    {
        $processedDirectors = new ArrayCollection();
        foreach ($directors as $key => $director) {
            $entry = $this->entityManager
                ->getRepository(Cast::class)
                ->findOneBy(['name' => $director['name']]);

            if (is_null($entry)) {
                $entry = new Directors();
                $entry->setName($director['name']);
                $this->entityManager->persist($entry);
            }

            $processedDirectors->set($key, $entry);
        }

        $this->entityManager->flush();

        $this->gallery->setDirectors($processedDirectors);
    }

    /**
     * @param string $url
     *
     * @return false|ApiMediaFile
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function generateTemporaryFile(string $url)
    {
        $fileContents = $this->getFileContents($url);

        if (!empty($fileContents)) {
            $guesser = MimeTypes::getDefault();
            $extensions = $guesser->getExtensions($fileContents['content-type']);
            $extension = $extensions[0] ?? null;

            $content = $fileContents['content'];
            $handle = tmpfile();
            fwrite($handle, $content);

            $file = new ApiMediaFile($handle);
            $file->setExtension($extension);
            $file->setMimetype($fileContents['content-type']);

            return $file;
        }

        return false;
    }

    /**
     * @param string $url
     *
     * @return array
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function getFileContents(string $url)
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
}

<?php

namespace App\Service;

use App\Entity\Cast;
use App\Entity\Directors;
use App\Entity\Gallery;
use App\Entity\GalleryItem;
use App\Entity\Genre;
use App\Entity\Media;
use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sonata\MediaBundle\Extra\ApiMediaFile;
use Sonata\MediaBundle\Model\MediaManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Throwable;

/**
 * MediaHandler Service
 *
 * Retrieves all data from a pre-specified endpoint and stores it locally via entities.
 */
class MediaHandler
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var MediaManagerInterface
     */
    private MediaManagerInterface $mediaManager;

    /**
     * @var Downloader
     */
    private Downloader $downloader;

    /**
     * @var Gallery
     */
    private Gallery $gallery;

    /**
     * MediaHandler constructor.
     *
     * @param MediaManagerInterface $mediaManager
     * @param Downloader $downloader
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(MediaManagerInterface $mediaManager, Downloader $downloader, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->mediaManager = $mediaManager;
        $this->downloader = $downloader;
    }

    /**
     * Fetch and process all the data that was retrieved.
     *
     * @param OutputInterface $output
     *   The console output.
     *
     * @throws Throwable
     */
    public function processData(OutputInterface $output)
    {
        $data = $this->downloader->fetchData();

        foreach ($data as $datum) {
            try {
                $this->entityManager->beginTransaction();

                $output->writeln(sprintf('Currently processing %s', $datum['headline']));

                $this->_buildGallery($datum);

                $this->_processImages($datum['cardImages']);

                $this->_processImages($datum['keyArtImages']);

                $output->writeln(sprintf('Finished processing processing %s', $datum['headline']));

                $this->entityManager->commit();
            } catch (Throwable $exception) {
                $this->entityManager->rollback();

                throw $exception;
            }
        }
    }

    /**
     * Helper method to build the Gallery object and store all the retrieved information within it.
     *
     * @param array $data
     *   The current data segment.
     *
     * @throws Exception
     */
    private function _buildGallery(array $data)
    {
        $this->gallery = new Gallery();
        $this->gallery
            ->setContext('default')
            ->setName($data['headline'])
            ->setBody($data['body'])
            ->setEnabled(true);

        if (isset($data['genres']) && is_array($data['genres'])) {
            $this->_processGenre($data['genres']);
        }

        if (isset($data['cast']) && is_array($data['cast'])) {
            $this->_processCast($data['cast']);
        }

        if (isset($data['directors']) && is_array($data['directors'])) {
            $this->_processDirectors($data['directors']);
        }

        if (isset($data['videos']) && is_array($data['videos'])) {
            $this->_processVideos($data['videos']);
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
    }

    /**
     * Helper method to process the given set of images into Media entries and link them to the current Gallery.
     *
     * @param array $images
     *   The images to process.
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     *
     * @see Media, Gallery
     */
    private function _processImages(array $images)
    {
        foreach ($images as $image) {
            $tmpFile = $this->_generateTemporaryFile($image['url']);

            if ($tmpFile === FALSE) {
                // @Todo: Log and display issues.
                continue;
            }

            $this->_buildMedia($tmpFile, basename($image['url']), $image['h'], $image['w']);
        }
    }

    /**
     * Helper method to retrieve or create a Genre entity to link to the current gallery.
     *
     * @param array $genres
     *   The genres to process.
     */
    private function _processGenre(array $genres)
    {
        foreach ($genres as $genre) {
            $entry = $this->entityManager
                ->getRepository(Genre::class)
                ->findOneBy(['name' => $genre]);

            if (is_null($entry)) {
                $entry = new Genre();
                $entry->setName($genre);
                $this->entityManager->persist($entry);
            }

            $this->gallery->getGenre()->add($entry);
        }
    }

    /**
     * Helper method to create a Video entity to link to the current gallery.
     *
     * @param array $videos
     *   The videos to process.
     */
    private function _processVideos(array $videos)
    {
        foreach ($videos as $video) {
            $entry = new Video();
            $entry
                ->setName($video['title'])
                ->setType($video['type'])
                ->setUrl($video['url'])
                ->setGallery($this->gallery);

            if (isset($video['alternatives'])) {
                $entry->setAlternatives($video['alternatives']);
            }

            $this->gallery->getVideos()->add($entry);
        }
    }

    /**
     * Helper method to retrieve or create a Cast entity to link to the current gallery.
     *
     * @param array $cast
     *   The cast to process.
     */
    private function _processCast(array $cast)
    {
        foreach ($cast as $castMember) {
            $entry = $this->entityManager
                ->getRepository(Cast::class)
                ->findOneBy(['name' => $castMember['name']]);

            if (is_null($entry)) {
                $entry = new Cast();
                $entry->setName($castMember['name']);
                $this->entityManager->persist($entry);
            }

            $this->gallery->getCast()->add($entry);
        }
    }

    /**
     * Helper method to retrieve or create a Director entity to link to the current gallery.
     *
     * @param array $directors
     *   The directors to process.
     */
    private function _processDirectors(array $directors)
    {
        foreach ($directors as $director) {
            $entry = $this->entityManager
                ->getRepository(Directors::class)
                ->findOneBy(['name' => $director['name']]);

            if (is_null($entry)) {
                $entry = new Directors();
                $entry->setName($director['name']);
                $this->entityManager->persist($entry);
            }

            $this->gallery->getDirectors()->add($entry);
        }
    }

    /**
     * Helper method to build a Media entry for the given file and link it to the currently processing Gallery.
     *
     * @param ApiMediaFile $temporaryFile
     *   The targeted temporary file.
     * @param string $name
     *   The name of the entry.
     * @param int $height
     *   The height of the entry.
     * @param int $width
     *   The width of the entry.
     *
     * @see Media, GalleryItem
     */
    private function _buildMedia(ApiMediaFile $temporaryFile, string $name, int $height, int $width)
    {
        /** @var Media $media */
        $media = $this->mediaManager->create();
        $media->setProviderName('sonata.media.provider.image');
        $media->setBinaryContent($temporaryFile);
        $media->setContext('default');
        $media->setHeight($height);
        $media->setWidth($width);
        $media->setName($name);
        $media->setEnabled(true);

        $this->mediaManager->save($media);

        $galleryItem = new GalleryItem();
        $galleryItem->setGallery($this->gallery);
        $galleryItem->setMedia($media);
        $galleryItem->setEnabled(true);

        // Manual persistence is required due to the doctrine being configured as part of the Sonata Core.
        $this->entityManager->persist($galleryItem);
    }

    /**
     * Helper method to read from the given URL and store information within a temporary file.
     *
     * @param string $url
     *   The targeted URL from which to retrieve data.
     *
     * @return false|ApiMediaFile
     *   A temporary stored file or false if an error has occurred.
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function _generateTemporaryFile(string $url)
    {
        $fileContents = $this->downloader->getFileContents($url);

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
}

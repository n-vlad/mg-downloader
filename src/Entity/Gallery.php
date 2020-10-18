<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sonata\MediaBundle\Entity\BaseGallery;
use Exception;

/**
 * The main Gallery entity that will be the center point for all downloaded information.
 *
 * @ORM\Entity
 * @ORM\Table(name="gallery")
 */
class Gallery extends BaseGallery
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * @ORM\Column(type="string")
     */
    protected string $rid;

    /**
     * @ORM\Column(type="text")
     */
    protected string $body;

    /**
     * @ORM\Column(type="string")
     */
    protected string $sum;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected string $cert;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected string $classType;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected int $duration;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected string $quote;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected int $rating;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected string $reviewAuthor;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected string $skyGoId;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected string $skyGoUrl;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected string $synopsis;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected string $url;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected int $year;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected DateTime $lastUpdated;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected array $viewingWindow;

    /**
     * @ORM\OneToMany(targetEntity="Video", mappedBy="gallery", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected Collection $videos;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Genre", cascade={"persist", "remove"})
     * @ORM\JoinTable(
     *     name="gallery_genre",
     *     joinColumns={@ORM\JoinColumn(name="gallery_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="genre_id", referencedColumnName="id")}
     *)
     */
    protected Collection $genre;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Cast", cascade={"persist", "remove"})
     * @ORM\JoinTable(
     *     name="gallery_cast",
     *     joinColumns={@ORM\JoinColumn(name="gallery_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="cast_id", referencedColumnName="id")}
     *)
     */
    protected Collection $cast;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Directors", cascade={"persist", "remove"})
     * @ORM\JoinTable(
     *     name="gallery_directors",
     *     joinColumns={@ORM\JoinColumn(name="gallery_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="director_id", referencedColumnName="id")}
     *)
     */
    protected Collection $directors;

    /**
     * Gallery constructor.
     */
    public function __construct()
    {
        $this->genre = new ArrayCollection();
        $this->cast = new ArrayCollection();
        $this->directors = new ArrayCollection();
        $this->videos = new ArrayCollection();

        parent::__construct();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getRid(): string
    {
        return $this->rid;
    }

    /**
     * @param string $rid
     *
     * @return self
     */
    public function setRid(string $rid): self
    {
        $this->rid = $rid;

        return $this;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     *
     * @return self
     */
    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return string
     */
    public function getCert(): string
    {
        return $this->cert;
    }

    /**
     * @param string $cert
     *
     * @return self
     */
    public function setCert(string $cert): self
    {
        $this->cert = $cert;

        return $this;
    }

    /**
     * @return string
     */
    public function getClassType(): string
    {
        return $this->classType;
    }

    /**
     * @param string $classType
     *
     * @return self
     */
    public function setClassType(string $classType): self
    {
        $this->classType = $classType;

        return $this;
    }

    /**
     * @return string
     */
    public function getQuote(): string
    {
        return $this->quote;
    }

    /**
     * @param string $quote
     *
     * @return self
     */
    public function setQuote(string $quote): self
    {
        $this->quote = $quote;

        return $this;
    }

    /**
     * @return int
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     *
     * @return self
     */
    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return int
     */
    public function getRating(): int
    {
        return $this->rating;
    }

    /**
     * @param int $rating
     *
     * @return self
     */
    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return string
     */
    public function getReviewAuthor(): string
    {
        return $this->reviewAuthor;
    }

    /**
     * @param string $reviewAuthor
     *
     * @return self
     */
    public function setReviewAuthor(string $reviewAuthor): self
    {
        $this->reviewAuthor = $reviewAuthor;

        return $this;
    }

    /**
     * @return string
     */
    public function getSkyGoId(): string
    {
        return $this->skyGoId;
    }

    /**
     * @param string $skyGoId
     *
     * @return self
     */
    public function setSkyGoId(string $skyGoId): self
    {
        $this->skyGoId = $skyGoId;

        return $this;
    }

    /**
     * @return string
     */
    public function getSkyGoUrl(): string
    {
        return $this->skyGoUrl;
    }

    /**
     * @param string $skyGoUrl
     *
     * @return self
     */
    public function setSkyGoUrl(string $skyGoUrl): self
    {
        $this->skyGoUrl = $skyGoUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getSum(): string
    {
        return $this->sum;
    }

    /**
     * @param string $sum
     *
     * @return self
     */
    public function setSum(string $sum): self
    {
        $this->sum = $sum;

        return $this;
    }

    /**
     * @return string
     */
    public function getSynopsis(): string
    {
        return $this->synopsis;
    }

    /**
     * @param string $synopsis
     *
     * @return self
     */
    public function setSynopsis(string $synopsis): self
    {
        $this->synopsis = $synopsis;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return self
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @param int $year
     *
     * @return self
     */
    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getLastUpdated(): DateTime
    {
        return $this->lastUpdated;
    }

    /**
     * @param string $lastUpdated
     *
     * @return self
     *
     * @throws Exception
     */
    public function setLastUpdated(string $lastUpdated): self
    {
        $this->lastUpdated = new DateTime($lastUpdated);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getGenre(): Collection
    {
        return $this->genre;
    }

    /**
     * @param Collection $genre
     *
     * @return self
     */
    public function setGenre(Collection $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getCast(): Collection
    {
        return $this->cast;
    }

    /**
     * @param Collection $cast
     *
     * @return self
     */
    public function setCast(Collection $cast): self
    {
        $this->cast = $cast;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getDirectors(): Collection
    {
        return $this->directors;
    }

    /**
     * @param Collection $directors
     *
     * @return self
     */
    public function setDirectors(Collection $directors): self
    {
        $this->directors = $directors;

        return $this;
    }

    /**
     * @param array $viewingWindow
     *
     * @return self
     */
    public function setViewingWindow(array $viewingWindow): self
    {
        $this->viewingWindow[] = $viewingWindow;

        return $this;
    }

    /**
     * @return array
     */
    public function getViewingWindow(): array
    {
        return $this->viewingWindow;
    }

    /**
     * @return Collection
     */
    public function setVideos(): Collection
    {
        return $this->videos;
    }

    /**
     * @return Collection
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }
}

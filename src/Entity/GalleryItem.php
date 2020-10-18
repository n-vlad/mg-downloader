<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\MediaBundle\Entity\BaseGalleryHasMedia;

/**
 * Relational table used to create a link between Gallery and Media items.
 *
 * @ORM\Entity
 * @ORM\Table(name="gallery_item")
 */
class GalleryItem extends BaseGalleryHasMedia
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}

<?php

namespace AmzsCMS\ArticleBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

trait DoctrineThumbnailTrait
{
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $thumbnail;


    /**
     * @return mixed
     */
    public function getThumbnail()
    {
        return $this->thumbnail ?? "themes/media/demo-thumb.png";
    }

    /**
     * @param mixed $thumbnail
     */
    public function setThumbnail($thumbnail): void
    {
        $this->thumbnail = $thumbnail;
    }
}
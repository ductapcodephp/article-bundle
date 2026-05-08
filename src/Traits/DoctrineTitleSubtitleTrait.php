<?php

namespace AmzsCMS\ArticleBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait DoctrineTitleSubtitleTrait
{
    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $title;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $subTitle = null;

    public function getTitle(): ?string
    {
        return $this->title ?? null;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getSubTitle(): ?string
    {
        return $this->subTitle;
    }

    public function setSubTitle(?string $subTitle): self
    {
        $this->subTitle = $subTitle;
        return $this;
    }
}
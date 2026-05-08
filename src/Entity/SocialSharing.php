<?php

namespace AmzsCMS\ArticleBundle\Entity;


use AmzsCMS\ArticleBundle\Traits\DoctrineIdentifierTrait;
use AmzsCMS\CoreBundle\Traits\Doctrine\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AmzsCMS\ArticleBundle\Repository\SocialSharingRepository")
 * @ORM\Table(name="core_social_sharing")
 * @ORM\HasLifecycleCallbacks
 */
class SocialSharing
{
    use DoctrineIdentifierTrait, Timestampable;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $googleTitle;

    /**
     * @ORM\OneToOne(targetEntity="AmzsCMS\ArticleBundle\Entity\Post", inversedBy="socialSharing")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id",nullable=true)
     */
    private $post;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $googleDescription;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $googleTag;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $facebookTitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $facebookDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $facebookThumbnail;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGoogleTitle(): ?string
    {
        return $this->googleTitle;
    }

    public function setGoogleTitle(?string $googleTitle): self
    {
        $this->googleTitle = $googleTitle;

        return $this;
    }

    public function getGoogleDescription(): ?string
    {
        return $this->googleDescription;
    }

    public function setGoogleDescription(?string $googleDescription): self
    {
        $this->googleDescription = $googleDescription;

        return $this;
    }

    public function getGoogleTag(): ?array
    {
        return $this->googleTag;
    }

    public function getGoogleTagStr(): string
    {
        if(is_null($this->googleTag)) return '';
        return implode(',', $this->googleTag);
    }

    public function setGoogleTag(?string $googleTag): self
    {
        if(is_null($googleTag)) return $this;
        $this->googleTag = array_column(json_decode($googleTag, true), 'value');

        return $this;
    }

    public function setGoogleTagStr(?string $googleTag): self
    {
        if(is_null($googleTag)) return $this;
        $this->googleTag = array_column(json_decode($googleTag, true), 'value');

        return $this;
    }

    public function getFacebookTitle(): ?string
    {
        return $this->facebookTitle;
    }

    public function setFacebookTitle(?string $facebookTitle): self
    {
        $this->facebookTitle = $facebookTitle;

        return $this;
    }

    public function getFacebookDescription(): ?string
    {
        return $this->facebookDescription;
    }

    public function setFacebookDescription(?string $facebookDescription): self
    {
        $this->facebookDescription = $facebookDescription;

        return $this;
    }

    public function getFacebookThumbnail(): ?string
    {
        return $this->facebookThumbnail;
    }

    public function setFacebookThumbnail(?string $facebookThumbnail): self
    {
        $this->facebookThumbnail = $facebookThumbnail;

        return $this;
    }



    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

}
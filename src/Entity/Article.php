<?php

namespace AmzsCMS\ArticleBundle\Entity;

use AmzsCMS\ArticleBundle\Traits\DoctrineContentTrait;
use AmzsCMS\ArticleBundle\Traits\DoctrineDescriptionTrait;
use AmzsCMS\ArticleBundle\Traits\DoctrineIdentifierTrait;
use AmzsCMS\ArticleBundle\Traits\DoctrineThumbnailTrait;
use AmzsCMS\ArticleBundle\Traits\DoctrineTitleSubtitleTrait;
use AmzsCMS\CoreBundle\Traits\Doctrine\Timestampable;
use AmzsCMS\ArticleBundle\DataType\PostStatusType;
use AmzsCMS\TopicBundle\Entity\Topic;
use AmzsCMS\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="AmzsCMS\ArticleBundle\Repository\ArticleRepository")
 * @ORM\Table(name="amzs_article")
 * @ORM\HasLifecycleCallbacks
 */
class Article
{
    use DoctrineTitleSubtitleTrait, DoctrineThumbnailTrait,
        DoctrineDescriptionTrait, DoctrineContentTrait, DoctrineIdentifierTrait, Timestampable;

    /**
     * @ORM\ManyToOne(targetEntity="AmzsCMS\UserBundle\Entity\User", inversedBy="articles")
     */
    private $author = null;

    /**
     * @ORM\OneToOne(targetEntity="AmzsCMS\ArticleBundle\Entity\SocialSharing", mappedBy="article", cascade={"persist", "remove"})
     */
    private $socialSharing;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Gedmo\Slug(fields={"title"}, updatable=false)
     */
    private $slug;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sortOrder = 1;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $isHot = PostStatusType::HOT_TYPE_NORMAL;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $isNew = PostStatusType::NEW_TYPE_NORMAL;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $postType;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $published = PostStatusType::PUBLISH_TYPE_PUBLISHED;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $tags;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $config;

    /**
     * @ORM\ManyToMany(targetEntity="AmzsCMS\TopicBundle\Entity\Topic", inversedBy="articles", cascade={"persist"})
     * @ORM\JoinTable(
     * name="amzs_article_topic",
     * joinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="topic_id", referencedColumnName="id")}
     * )
     */
    private $topics;

    /**
     * @Gedmo\Locale
     */
    private $locale;

    public function __construct()
    {
        $this->topics = new ArrayCollection();
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function setAuthor(?User $author): void
    {
        $this->author = $author;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(?int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }

    public function getIsHot()
    {
        return $this->isHot;
    }

    public function setIsHot($isHot): self
    {
        $this->isHot = (is_string($isHot) && $isHot == 'on')
            ? PostStatusType::HOT_TYPE_HOT
            : $isHot;
        return $this;
    }

    public function getIsNew()
    {
        return $this->isNew;
    }

    public function setIsNew($isNew): self
    {
        $this->isNew = (is_string($isNew) && $isNew == 'on')
            ? PostStatusType::NEW_TYPE_NEW
            : $isNew;
        return $this;
    }

    public function getPostType()
    {
        return $this->postType;
    }

    public function setPostType(?string $postType): self
    {
        $this->postType = $postType;
        return $this;
    }

    public function getPublished(): int
    {
        return (int)$this->published;
    }

    public function setPublished(?string $published): self
    {
        $this->published = $published;
        return $this;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setTags($tags): void
    {
        $this->tags = $tags;
    }

    public function getArrTags(): string
    {
        return is_null($this->tags) ? '' : implode(',', ($this->tags));
    }

    public function setArrTags(?string $tags): void
    {
        if (is_null($tags) || $tags === '') {
            return;
        }

        $arr = json_decode($tags, true);
        if (!is_array($arr)) {
            $this->tags = array_filter(array_map('trim', explode(',', $tags)));
            return;
        }

        $this->tags = array_column($arr, 'value');
    }

    public function getConfig(): ?string
    {
        return $this->config;
    }

    public function setConfig(?string $config): self
    {
        $this->config = $config;
        return $this;
    }

    public function getSocialSharing(): ?SocialSharing
    {
        return $this->socialSharing;
    }

    public function setSocialSharing(?SocialSharing $socialSharing): self
    {
        if ($socialSharing === null && $this->socialSharing !== null) {
            $this->socialSharing->setArticle(null);
        }

        if ($socialSharing !== null && $socialSharing->getArticle() !== $this) {
            $socialSharing->setArticle($this);
        }

        $this->socialSharing = $socialSharing;
        return $this;
    }

    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function addTopic(Topic $topic): self
    {
        if (!$this->topics->contains($topic)) {
            $this->topics->add($topic);
        }
        return $this;
    }

    public function removeTopic(Topic $topic): self
    {
        $this->topics->removeElement($topic);
        return $this;
    }
}
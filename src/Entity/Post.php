<?php

namespace AmzsCMS\ArticleBundle\Entity;



use AmzsCMS\ArticleBundle\Traits\DoctrineContentTrait;
use AmzsCMS\ArticleBundle\Traits\DoctrineDescriptionTrait;
use AmzsCMS\ArticleBundle\Traits\DoctrineIdentifierTrait;
use AmzsCMS\ArticleBundle\Traits\DoctrineThumbnailTrait;
use AmzsCMS\ArticleBundle\Traits\DoctrineTitleSubtitleTrait;
use AmzsCMS\CoreBundle\Traits\Doctrine\Timestampable;
use AmzsCMS\PageBundle\Entity\Page;
use AmzsCMS\TopicBundle\Entity\Topic;
use Doctrine\Common\Collections\ArrayCollection;
use AmzsCMS\ArticleBundle\DataType\ArticleStatusType;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="AmzsCMS\ArticleBundle\Repository\PostRepository")
 * @ORM\Table(name="amzs_post")
 * @ORM\HasLifecycleCallbacks
 */
class Post
{
    use DoctrineTitleSubtitleTrait, DoctrineThumbnailTrait,
        DoctrineDescriptionTrait, DoctrineContentTrait,  DoctrineIdentifierTrait,Timestampable;

    /**
     * @ORM\OneToOne(targetEntity="AmzsCMS\ArticleBundle\Entity\Article", mappedBy="post")
     */
    private $article;

    /**
     * @ORM\OneToOne(targetEntity="AmzsCMS\ArticleBundle\Entity\SocialSharing", mappedBy="post", cascade={"persist", "remove"})
     */
    private $socialSharing;

    /**
     * @ORM\OneToOne(targetEntity="AmzsCMS\PageBundle\Entity\Page", mappedBy="post",cascade={"persist"}  )
     */
    private $page;

//    /**
//     * @ORM\ManyToOne (targetEntity="AmzsCMS\ArticleBundle\Entity\Category", inversedBy="posts" )
//     * @ORM\JoinColumn(name="category_id", referencedColumnName="id",nullable=true)
//     */
//    private $category;

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
    private $isHot = ArticleStatusType::HOT_TYPE_NORMAL;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $isNew = ArticleStatusType::NEW_TYPE_NORMAL;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $postType;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $published = ArticleStatusType::PUBLISH_TYPE_PUBLISHED;

//    /**
//     * @ORM\OneToOne(targetEntity="AmzsCMS\ArticleBundle\Entity\Gallery",inversedBy ="post")
//     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id",nullable=true)
//     */
//    private $gallery;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $tags;

//    /**
//     * @ORM\OneToMany(targetEntity="AmzsCMS\ArticleBundle\Entity\Block", mappedBy="post", cascade={"persist"})
//     * @ORM\OrderBy({"sortOrder" = "ASC"})
//     */
//    private $blocks;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $config;
    /**
     * @ORM\ManyToMany(targetEntity="AmzsCMS\TopicBundle\Entity\Topic", inversedBy="posts", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="amzs_post_topic",
     *     joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="topic_id", referencedColumnName="id")}
     * )
     */
    private $topics;
    /**
     * @Gedmo\Locale
     */
    private $locale;

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }
    public function __construct()
    {
        $this->blocks = new ArrayCollection();
        $this->topics = new ArrayCollection();
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
            ? ArticleStatusType::HOT_TYPE_HOT
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
            ? ArticleStatusType::NEW_TYPE_NEW
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
        return $this->published;
    }

    public function setPublished(?string $published): self
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param mixed $tags
     */
    public function setTags($tags): void
    {
        $this->tags = $tags;
    }

    public function getArrTags(): string
    {
        return is_null($this->tags) ? '' : implode(',', ($this->tags));
    }

    /**
     * @param mixed $tags
     */
    public function setArrTags(?string $tags): void
    {
        if (is_null($tags) || $tags === '') {
            return;
        }

        $arr = json_decode($tags, true);

        if (!is_array($arr)) {
            // Trường hợp tags là chuỗi thường: "tag1,tag2"
            $this->tags = array_filter(array_map('trim', explode(',', $tags)));
            return;
        }

        $this->tags = array_column($arr, 'value');
    }

    public function getConfig(): ?string
    {
        return $this->config;
    }

    public function setConfig(?string $config)
    {
        $this->config = $config;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article)
    {
        // unset the owning side of the relation if necessary
        if ($article === null && $this->article !== null) {
            $this->article->setPost(null);
        }

        // set the owning side of the relation if necessary
        if ($article !== null && $article->getPost() !== $this) {
            $article->setPost($this);
        }

        $this->article = $article;

        return $this;
    }

    public function getSocialSharing(): ?SocialSharing
    {
        return $this->socialSharing;
    }

    public function setSocialSharing(?SocialSharing $socialSharing)
    {
        // unset the owning side of the relation if necessary
        if ($socialSharing === null && $this->socialSharing !== null) {
            $this->socialSharing->setPost(null);
        }

        // set the owning side of the relation if necessary
        if ($socialSharing !== null && $socialSharing->getPost() !== $this) {
            $socialSharing->setPost($this);
        }

        $this->socialSharing = $socialSharing;

        return $this;
    }

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page)
    {
        // unset the owning side of the relation if necessary
        if ($page === null && $this->page !== null) {
            $this->page->setPost(null);
        }

        // set the owning side of the relation if necessary
        if ($page !== null && $page->getPost() !== $this) {
            $page->setPost($this);
        }

        $this->page = $page;

        return $this;
    }

//    public function getCategory(): ?Category
//    {
//        return $this->category;
//    }
//
//    public function setCategory(?Category $category)
//    {
//        $this->category = $category;
//
//        return $this;
//    }
//
//    public function getGallery(): ?Gallery
//    {
//        return $this->gallery;
//    }
//
//    public function setGallery(?Gallery $gallery)
//    {
//        $this->gallery = $gallery;
//
//        return $this;
//    }
//
//    /**
//     * @return Collection<int, Block>
//     */
//    public function getBlocks(): Collection
//    {
//        $criteria = Criteria::create();
//        $criteria->andWhere(Criteria::expr()->eq('isArchived', ArchivedDataType::UN_ARCHIVED));
//        return $this->blocks->matching($criteria);
//    }
//
//    public function addBlock(Block $block)
//    {
//        if (!$this->blocks->contains($block)) {
//            $this->blocks->add($block);
//            $block->setPost($this);
//        }
//
//        return $this;
//    }
//
//    public function removeBlock(Block $block)
//    {
//        if ($this->blocks->removeElement($block)) {
//            // set the owning side to null (unless already changed)
//            if ($block->getPost() === $this) {
//                $block->setPost(null);
//            }
//        }
//
//        return $this;
//    }

    public function getTopics(): ArrayCollection
    {
        return $this->topics;
    }

    public function addTopic(Topic $topic)
    {
        if (!$this->topics->contains($topic)) {
            $this->topics->add($topic);
        }

        return $this;
    }

    public function removeTopic(Topic $topic)
    {
        $this->topics->removeElement($topic);

        return $this;
    }

}

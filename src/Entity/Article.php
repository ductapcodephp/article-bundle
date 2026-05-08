<?php

namespace AmzsCMS\ArticleBundle\Entity;

use AmzsCMS\ArticleBundle\Traits\DoctrineIdentifierTrait;
use AmzsCMS\CoreBundle\Traits\Doctrine\Timestampable;
use AmzsCMS\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AmzsCMS\ArticleBundle\Repository\ArticleRepository")
 * @ORM\Table(name="amzs_article")
 * @ORM\HasLifecycleCallbacks
 */
class Article
{
    use Timestampable,DoctrineIdentifierTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AmzsCMS\UserBundle\Entity\User", inversedBy="articles")
     */
    private $author = null;

    /**
     * @ORM\OneToOne(targetEntity="AmzsCMS\ArticleBundle\Entity\Post", inversedBy="article", cascade={"persist", "remove"})
     */
    private $post;

    /**
     * @param User|null $author
     */
    public function setAuthor(?User $author): void
    {
        $this->author = $author;
    }

    /**
     * @return User|null
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @return Post|null
     */
    public function getPost(): ?Post
    {
        return $this->post;
    }

    /**
     * @param Post|null $post
     */
    public function setPost(?Post $post): void
    {
        $this->post = $post;
    }
}
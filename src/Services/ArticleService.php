<?php

namespace AmzsCMS\ArticleBundle\Services;

use AmzsCMS\ArticleBundle\Repository\ArticleRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Symfony\Component\HttpFoundation\RequestStack;

class ArticleService
{
    private $articleRepository;
    private $requestStack;
    public function __construct(ArticleRepository $articleRepository, RequestStack $requestStack)
    {
        $this->articleRepository = $articleRepository;
        $this->requestStack = $requestStack;
    }

    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return $this->articleRepository->find($id, $lockMode, $lockVersion);
    }

    public function findOneBy($criteria, $orderBy = null)
    {
        return $this->articleRepository->findOneBy($criteria, $orderBy);
    }

    public function findBySlug($slug, $hintReadOnly = false)
    {
//        $qb = $this->createQueryBuilder('page');
//        $qb->join('page.post', 'post');
//        $qb->where(
//            $qb->expr()->eq('post.slug', $qb->expr()->literal($slug)),
//            $qb->expr()->eq('post.isArchived', $qb->expr()->literal(ArchivedDataType::UN_ARCHIVED)),
//            $qb->expr()->eq('post.published', $qb->expr()->literal(PostStatusType::PUBLISH_TYPE_PUBLISHED)),
//        );
        try{
            return $this->articleRepository->findBySlug($slug)
                ->getQuery()
                ->setHint(Query::HINT_READ_ONLY, $hintReadOnly)
                ->getOneOrNullResult();
        }catch (NonUniqueResultException $ex) {
            return null;
        }
    }

    private function getLocale()
    {
        return $this->requestStack->getCurrentRequest()->getLocale();
    }

    public function getPaginated($keyword, $filters = null)
    {
        return $this->articleRepository->getPaginated($keyword, $filters)
            ->getQuery()->setHint(Query::HINT_READ_ONLY, true);
    }
}
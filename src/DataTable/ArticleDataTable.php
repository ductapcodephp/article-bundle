<?php

namespace AmzsCMS\ArticleBundle\DataTable;

use AmzsCMS\ArticleBundle\DataType\ArticleStatusType;
use AmzsCMS\ArticleBundle\Entity\Article;
use AmzsCMS\ArticleBundle\Repository\ArticleRepository;
use AmzsCMS\CoreBundle\Service\Datatable\BaseDataTable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ArticleDataTable extends BaseDataTable
{
    protected $entityAlias = 'article';
    private  $translatableListener;
    private  $parameterBag;
    private $csrfTokenManager;
    private $defaultLocale;
    public function __construct(ArticleRepository $repository,
        EntityManagerInterface $em,
        TranslatableListener $translatableListener,
        ParameterBagInterface $parameterBag,
        CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->translatableListener = $translatableListener;
        $this->parameterBag = $parameterBag;
        $this->defaultLocale = $parameterBag->get('language')['default'];
        $this->csrfTokenManager = $csrfTokenManager;
        parent::__construct($repository);
    }

    // ================== Tùy chỉnh QueryBuilder ==================
    protected function createBaseQueryBuilder(): QueryBuilder
    {
        return $this->repository->createQueryBuilder($this->entityAlias)
            ->join($this->entityAlias . '.post', 'post')
            ->andWhere('post.deletedAt IS NULL');
    }
    protected function applyDefaultFilters(QueryBuilder $qb, Request $request): void
    {
    }
    protected function applyCustomFilters(QueryBuilder $qb, Request $request): void
    {
        $locale = $request->query->get('language');

        if (empty($locale)) {
            return;
        }
        $this->translatableListener->setTranslatableLocale($locale);
        if ($locale === $this->defaultLocale) {
            return;
        }
    }

    protected function getColumnMap(): array
    {
        return [
            0 => 'createdAt',
        ];
    }

    protected function getSearchableFields(): array
    {
        return ['name'];
    }

    protected function formatData(array $entities): array
    {
        $data = [];
        /** @var Article $article */
        foreach ($entities as $index => $article) {
            $data[] = [
                'index'         => $index + 1,
                'id'            => $article->getId(),
                'article_title' => $article->getPost()->getTitle(),
                'thumbnail'     => $article->getPost()->getThumbnail(),
                'hot'           => ArticleStatusType::getNameHotType($article->getPost()->getIsHot()),
                'new'           => ArticleStatusType::getNameNewType($article->getPost()->getIsNew()),
                'created_at'    => $article->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at'    => $article->getUpdatedAt()->format('Y-m-d H:i:s'),
                '_csrf_token' => $this->csrfTokenManager->getToken('delete-article-'.$article->getId())->getValue(),
            ];
        }
        return $data;
    }
}
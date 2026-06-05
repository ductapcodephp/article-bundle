<?php

namespace AmzsCMS\ArticleBundle\DataTable;

use AmzsCMS\ArticleBundle\DataType\PostStatusType;
use AmzsCMS\ArticleBundle\Entity\Article;
use AmzsCMS\ArticleBundle\Repository\ArticleRepository;
use AmzsCMS\CoreBundle\Service\Datatable\BaseDataTable;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ArticleDataTable extends BaseDataTable
{
    protected $entityAlias = 'article';
    private $translatableListener;
    private $parameterBag;
    private $csrfTokenManager;
    private $defaultLocale;

    public function __construct(
        ArticleRepository $repository,
        TranslatableListener $translatableListener,
        ParameterBagInterface $parameterBag,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->translatableListener = $translatableListener;
        $this->parameterBag = $parameterBag;
        $this->defaultLocale = $parameterBag->get('language')['default'] ?? 'vi';
        $this->csrfTokenManager = $csrfTokenManager;
        parent::__construct($repository);
    }

    protected function createBaseQueryBuilder(): QueryBuilder
    {
        return $this->repository->createQueryBuilder($this->entityAlias);
    }

    protected function applyDefaultFilters(QueryBuilder $qb, Request $request): void
    {
    }

    protected function applyCustomFilters(QueryBuilder $qb, Request $request): void
    {
        $locale = $request->query->get('language');

        if (empty($locale)) {
            $locale = $this->defaultLocale;
        }
        $this->translatableListener->setTranslatableLocale($locale);

    }

    protected function getColumnMap(): array
    {
        return [
            0 => 'createdAt',
        ];
    }

    protected function getSearchableFields(): array
    {
        return ['title'];
    }

    protected function formatData(array $entities): array
    {
        $data = [];
        /** @var Article $article */
        foreach ($entities as $index => $article) {
            $data[] = [
                'index'         => $index + 1,
                'id'            => $article->getId(),
                'article_title' => $article->getTitle(),
                'thumbnail'     => $article->getThumbnail(),
                'hot'           => PostStatusType::getNameHotType((int)$article->getIsHot()),
                'new'           => PostStatusType::getNameNewType((int)$article->getIsNew()),
                'created_at'    => $article->getCreatedAt() ? $article->getCreatedAt()->format('Y-m-d H:i:s') : null,
                'updated_at'    => $article->getUpdatedAt() ? $article->getUpdatedAt()->format('Y-m-d H:i:s') : null,
                '_csrf_token'   => $this->csrfTokenManager->getToken('delete-article-' . $article->getId())->getValue(),
            ];
        }
        return $data;
    }
}
<?php

declare(strict_types=1);

namespace AmzsCMS\ArticleBundle\Controller;

use AmzsCMS\ArticleBundle\Constant\ArticleRoute;
use AmzsCMS\ArticleBundle\DataTable\ArticleDataTable;
use AmzsCMS\ArticleBundle\Entity\Article;
use AmzsCMS\ArticleBundle\Entity\SocialSharing;
use AmzsCMS\ArticleBundle\Form\AddArticleForm;
use AmzsCMS\ArticleBundle\Services\ArticleService;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Translatable\Entity\Translation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ArticleController extends AbstractController
{
    private array $locales;
    private $articleService;
    private $entityManager;

    public function __construct(
        ArticleService $articleService,
        EntityManagerInterface $entityManager,
        ParameterBagInterface $parameterBag
    ) {
        $this->entityManager  = $entityManager;
        $this->articleService = $articleService;
        $langConfig           = $parameterBag->get('language');
        $this->locales        = $langConfig['locales'] ?? ['vi'];
    }

    private function loadTranslations(Article $article): array
    {
        $translationRepo = $this->entityManager->getRepository(Translation::class);
        $allTranslations = $translationRepo->findTranslations($article);

        $translations = [];
        foreach ($this->locales as $locale) {
            $translations[$locale] = [
                'title'       => $allTranslations[$locale]['title'] ?? $article->getTitle(),
                'description' => $allTranslations[$locale]['description'] ?? $article->getDescription(),
                'content'     => $allTranslations[$locale]['content'] ?? $article->getContent(),
            ];
        }

        return $translations;
    }
    private function saveTranslations(Article $article, $form): void
    {
        $slugify     = new Slugify();
        $firstLocale = true;

        foreach ($this->locales as $locale) {
            $title       = $form->get("title_{$locale}")->getData();
            $description = $form->get("description_{$locale}")->getData();
            $content     = $form->get("content_{$locale}")->getData();

            $article->setLocale($locale);
            $article->setTitle($title ?? '');
            $article->setDescription($description);
            $article->setContent($content);

            if ($firstLocale && empty($article->getSlug())) {
                $article->setSlug($slugify->slugify($title ?? ''));
                $firstLocale = false;
            }

            $this->entityManager->persist($article);
            $this->entityManager->flush();
        }
    }

    public function index(): Response
    {
        return $this->render('@AmzsArticle/article/index.html.twig', [
            'locales' => $this->locales,
        ]);
    }

    public function add(Request $request): Response
    {
        $article = new Article();

        $socialSharing = new SocialSharing();
        $socialSharing->setArticle($article);
        $article->setSocialSharing($socialSharing);

        $translations = [];
        foreach ($this->locales as $locale) {
            $translations[$locale] = [
                'title'       => null,
                'description' => null,
                'content'     => null,
            ];
        }

        $form = $this->createForm(AddArticleForm::class, $article, [
            'locales'      => $this->locales,
            'translations' => $translations,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $article->setAuthor($this->getUser());
                $this->entityManager->persist($article);
                $this->entityManager->flush();

                $this->saveTranslations($article, $form);

                return new JsonResponse([
                    'message'  => 'Article added successfully!',
                    'redirect' => $this->generateUrl(ArticleRoute::ROUTE_EDIT, ['id' => $article->getId()])
                ]);
            }

            return new JsonResponse([
                'message' => 'Please check the form data again.',
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->render('@AmzsArticle/article/add_or_edit_article.html.twig', [
            'title'   => 'Add article',
            'article' => null,
            'form'    => $form->createView(),
            'locales' => $this->locales,
        ]);
    }

    public function data(Request $request, ArticleDataTable $articleDataTable): Response
    {
        return $this->json($articleDataTable->getData($request));
    }

    public function edit(Request $request, int $id): Response
    {
        $article = $this->articleService->find($id);

        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }

        if ($article->getSocialSharing() === null) {
            $socialSharing = new SocialSharing();
            $socialSharing->setArticle($article);
            $article->setSocialSharing($socialSharing);
        }

        $translations = $this->loadTranslations($article);

        $form = $this->createForm(AddArticleForm::class, $article, [
            'locales'      => $this->locales,
            'translations' => $translations,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $keepSlug    = $request->request->get('keepSlug');
                $currentSlug = $article->getSlug();

                $this->saveTranslations($article, $form);

                if (!empty($keepSlug)) {
                    $article->setSlug($currentSlug);
                    $this->entityManager->flush();
                }

                return new JsonResponse(['message' => 'Article updated successfully!']);
            }

            return new JsonResponse([
                'message' => 'Please check the form data again.',
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->render('@AmzsArticle/article/add_or_edit_article.html.twig', [
            'title'   => 'Edit article',
            'article' => $article,
            'form'    => $form->createView(),
            'locales' => $this->locales,
        ]);
    }

    public function delete(Request $request, int $id): Response
    {
        $article = $this->articleService->find($id);

        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }

        $csrfToken = $request->query->get('_csrf_token');

        if (!$this->isCsrfTokenValid('delete-article-' . $id, $csrfToken)) {
            throw new AccessDeniedHttpException();
        }

        $this->entityManager->remove($article);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'Article deleted successfully'
        ]);
    }
}
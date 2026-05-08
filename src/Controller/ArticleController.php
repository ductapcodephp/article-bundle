<?php

declare(strict_types=1);

namespace AmzsCMS\ArticleBundle\Controller;

use AmzsCMS\ArticleBundle\Constant\ArticleRoute;
use AmzsCMS\ArticleBundle\DataTable\ArticleDataTable;
use AmzsCMS\ArticleBundle\Entity\Article;
use AmzsCMS\ArticleBundle\Entity\Post;
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

    private function loadTranslations(Post $post): array
    {
        $translationRepo = $this->entityManager->getRepository(Translation::class);
        $allTranslations = $translationRepo->findTranslations($post);

        $translations = [];
        foreach ($this->locales as $locale) {
            $translations[$locale] = [
                'title'       => $allTranslations[$locale]['title'] ?? $post->getTitle(),
                'description' => $allTranslations[$locale]['description'] ?? $post->getDescription(),
                'content'     => $allTranslations[$locale]['content'] ?? $post->getContent(),
            ];
        }

        return $translations;
    }

    private function saveTranslations(Post $post, $postForm): void
    {
        $slugify     = new Slugify();
        $firstLocale = true;

        foreach ($this->locales as $locale) {
            $title       = $postForm->get("title_{$locale}")->getData();
            $description = $postForm->get("description_{$locale}")->getData();
            $content     = $postForm->get("content_{$locale}")->getData();

            $post->setLocale($locale);
            $post->setTitle($title ?? '');
            $post->setDescription($description);
            $post->setContent($content);

            if ($firstLocale && empty($post->getSlug())) {
                $post->setSlug($slugify->slugify($title ?? ''));
                $firstLocale = false;
            }

            $this->entityManager->persist($post);
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
        $post    = new Post();
        $article->setPost($post);
        $post->setArticle($article);

        $socialSharing = new SocialSharing();
        $socialSharing->setPost($post);
        $post->setSocialSharing($socialSharing);

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

                $this->saveTranslations($post, $form->get('post'));

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

        if (!$article->getPost()) {
            $post = new Post();
            $article->setPost($post);
            $post->setArticle($article);
        }

        $post = $article->getPost();

        if ($post->getSocialSharing() === null) {
            $socialSharing = new SocialSharing();
            $socialSharing->setPost($post);
            $post->setSocialSharing($socialSharing);
        }

        $translations = $this->loadTranslations($post);
        $form = $this->createForm(AddArticleForm::class, $article, [
            'locales'      => $this->locales,
            'translations' => $translations,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $keepSlug    = $request->request->get('keepSlug');
                $currentSlug = $post->getSlug();

                $this->saveTranslations($post, $form->get('post'));

                if (!empty($keepSlug)) {
                    $post->setSlug($currentSlug);
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
<?php

declare(strict_types=1);

namespace AmzsCMS\ArticleBundle\Form;

use AmzsCMS\ArticleBundle\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddArticleForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $article = $options['data'] ?? null;

        $builder->add('post', PostType::class, [
            'data'         => $article ? $article->getPost() : null,
            'locales'      => $options['locales'],
            'translations' => $options['translations'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'   => Article::class,
            'locales'      => ['vi'],
            'translations' => [],
        ]);
    }
}
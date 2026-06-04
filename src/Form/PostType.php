<?php

declare(strict_types=1);

namespace AmzsCMS\ArticleBundle\Form;

use AmzsCMS\ArticleBundle\DataType\ArticleStatusType;
use AmzsCMS\ArticleBundle\Entity\Post;
use AmzsCMS\ArticleBundle\Form\Common\CkeditorType;
use AmzsCMS\ArticleBundle\Form\Common\PublishedChoiceType;
use AmzsCMS\ArticleBundle\Form\Common\TagType;
use AmzsCMS\ArticleBundle\Form\Common\TopicChoiceType;
use AmzsCMS\ArticleBundle\Form\Common\SocialSharingType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $post         = $options['data'];
        $locales      = $options['locales'];
        $translations = $options['translations'];

        $builder->add('thumbnail', HiddenType::class, ['required' => false]);
        $builder->add('arrTags', TagType::class, ['required' => false]);
        $builder->add('topics', TopicChoiceType::class, [
            'label'    => 'Topics',
            'required' => false,
        ]);
        $builder->add('published', PublishedChoiceType::class, [
            'attr' => [
                'data-select2-dropdown-parent-value' => '#article-form',
            ],
            'data' => $post instanceof Post
                ? $post->getPublished()
                : ArticleStatusType::PUBLISH_TYPE_PUBLISHED,
        ]);
        $builder->add('isHot', HiddenType::class, ['required' => false]);
        $builder->add('isNew', HiddenType::class, ['required' => false]);
        $builder->add('socialSharing', SocialSharingType::class);

        foreach ($locales as $locale) {
            $builder->add("title_{$locale}", TextType::class, [
                'mapped'   => false,
                'required' => false,
                'label'    => 'Title (' . strtoupper($locale) . ')',
                'data'     => $translations[$locale]['title'] ?? null,
                'attr'     => [
                    'class'           => 'form-control mb-2',
                    'data-locale'     => $locale,
                    'data-field-type' => 'title',
                ],
            ]);
            $builder->add("description_{$locale}", TextareaType::class, [
                'mapped'   => false,
                'required' => false,
                'label'    => 'Description (' . strtoupper($locale) . ')',
                'data'     => $translations[$locale]['description'] ?? null,
                'attr'     => [
                    'class'       => 'mb-2 form-control',
                    'rows'        => 5,
                    'data-locale' => $locale,
                ],
            ]);
            $builder->add("content_{$locale}", CkeditorType::class, [
                'mapped'   => false,
                'required' => false,
                'label'    => 'Content (' . strtoupper($locale) . ')',
                'data'     => $translations[$locale]['content'] ?? null,
                'attr'     => [
                    'data-locale'       => $locale,
                    'data-amz-ckeditor' => 'true',

                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'   => Post::class,
            'locales'      => ['vi'],
            'translations' => [],
        ]);
    }
}
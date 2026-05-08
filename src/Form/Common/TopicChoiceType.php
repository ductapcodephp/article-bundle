<?php

namespace AmzsCMS\ArticleBundle\Form\Common;

use AmzsCMS\TopicBundle\Entity\Topic;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TopicChoiceType extends AbstractType
{
    public function getParent(): string
    {
        return EntityType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Topic::class,
            'choice_label' => 'name',
            'multiple' => true,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('t')
                    ->andWhere('t.deletedAt IS NULL')
                    ->orderBy('t.name', 'ASC');
            },

            'attr' => [
                'data-controller' => 'select2',
                'data-select2-placeholder-value' => '-- Chọn chủ đề --',
                'class' => 'form-select form-select-sm',
            ],
            'placeholder' => 'Chọn chủ đề',
        ]);

        $resolver->setDefined(['data-select2-dropdown-parent-value']);
        $resolver->setDefined(['data-select2-hidden-search-value']);

        $resolver->setNormalizer('attr', function (Options $options, $value) {
            if (isset($options['data-select2-dropdown-parent-value'])) {
                $value['data-select2-dropdown-parent-value'] = $options['data-select2-dropdown-parent-value'];
            }
            if (isset($options['data-select2-hidden-search-value'])) {
                $value['data-select2-hidden-search-value'] = $options['data-select2-hidden-search-value'];
            }
            return $value;
        });
    }
}
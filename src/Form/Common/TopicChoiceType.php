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

            'required' => false,

            'expanded' => false,

            'query_builder' => function (EntityRepository $er) {

                return $er->createQueryBuilder('t')
                    ->andWhere('t.deletedAt IS NULL')
                    ->orderBy('t.name', 'ASC');
            },

            'attr' => [
                'class' => 'form-select js-select2-topics',
            ],
        ]);

    }
}
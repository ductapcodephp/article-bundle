<?php

declare(strict_types=1);

namespace AmzsCMS\ArticleBundle\Form\Common;

use AmzsCMS\ArticleBundle\Entity\SocialSharing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SocialSharingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('googleTitle', TextType::class, ['required' => false]);
        $builder->add('googleTagStr', TagType::class, ['required' => false]);
        $builder->add('googleDescription', TextareaType::class, [
            'required' => false,
            'attr' => [
                'rows' => 5,
                'class' => 'form-control fs-7',
                'placeholder' => 'Enter Description',
            ]
        ]);

        $builder->add('facebookTitle', TextType::class, ['required' => false]);
        $builder->add('facebookThumbnail', HiddenType::class, ['required' => false]);
        $builder->add('facebookDescription', TextareaType::class,  [
            'required' => false,
            'attr' => [
                'rows' => 5,
                'class' => 'form-control fs-7',
                'placeholder' => 'Enter Description',
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SocialSharing::class,
        ]);
    }
}

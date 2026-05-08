<?php

declare(strict_types=1);

namespace AmzsCMS\ArticleBundle\Form\Common;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagType extends AbstractType
{
    public function getParent(): string
    {
        return TextType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'Tags',
            'attr' => [
                'id' => 'tags',
                'placeholder' => 'Enter tags',
                'class' => 'form-control form-control-sm form-control-solid mb-2',
            ]
        ]);
    }
}

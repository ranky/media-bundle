<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Dummy\Page\Presentation\Form;

use Ranky\MediaBundle\Presentation\Form\RankyMediaFileManagerType;
use Ranky\MediaBundle\Tests\Dummy\Page\Domain\Page;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string,mixed> $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('mediaId', RankyMediaFileManagerType::class, [
            'required'   => false,
            'label'      => 'Featured Image',
            'api_prefix' => '/admin',
        ]);

        $builder->add('gallery', RankyMediaFileManagerType::class, [
            'required'           => false,
            'label'              => 'Gallery JSON Array',
            'api_prefix'         => '/admin',
            'modal_title'        => 'Media File Manager',
            'multiple_selection' => true,
        ]);


        $builder->add('media', RankyMediaFileManagerType::class, [
            'required'           => false,
            'label'              => 'Featured Image',
            'api_prefix'         => '/admin',
            'modal_title'        => 'Media File Manager',
            'association'        => true,
            'multiple_selection' => false,
        ]);

        $builder->add('medias', RankyMediaFileManagerType::class, [
            'required'           => false,
            'label'              => 'Media Collection',
            'api_prefix'         => '/admin',
            'modal_title'        => 'Media File Manager',
            'association'        => true,
            'multiple_selection' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
        ]);
    }
}

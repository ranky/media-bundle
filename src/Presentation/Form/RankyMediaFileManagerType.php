<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Presentation\Form;

use Ranky\MediaBundle\Domain\Contract\MediaRepositoryInterface;
use Ranky\MediaBundle\Presentation\Form\DataTransformer\JsonToArrayTransformer;
use Ranky\MediaBundle\Presentation\Form\DataTransformer\MediaCollectionTransformer;
use Ranky\MediaBundle\Presentation\Form\DataTransformer\MediaEntityTransformer;
use Ranky\MediaBundle\Presentation\Form\DataTransformer\MediaIdToStringTransformer;
use Ranky\MediaBundle\Presentation\Form\DataTransformer\MediaPathTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RankyMediaFileManagerType extends AbstractType
{

    public function __construct(protected readonly MediaRepositoryInterface $mediaRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['association'] === true) {
            if ($options['save_path'] === true) {
                throw new \InvalidArgumentException('The option "save_path" cannot be used with "association"');
            }
            if ($options['multiple_selection'] === true) {
                $builder->addModelTransformer(new MediaCollectionTransformer($this->mediaRepository));
            } else {
                $builder->addModelTransformer(new MediaEntityTransformer($this->mediaRepository));
            }
        } elseif ($options['save_path'] === true) {
            $builder->addModelTransformer(
                new MediaPathTransformer(
                    $this->mediaRepository,
                    $options['multiple_selection']
                )
            );
        } elseif ($options['multiple_selection'] === true) {
            $builder->addModelTransformer(new JsonToArrayTransformer());
        } else {
            $builder->addViewTransformer(new MediaIdToStringTransformer());
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'compound'           => false,
            'multiple_selection' => false,
            'modal_title'        => 'Media File Manager',
            'association'        => false,
            'api_prefix'         => null,
            'save_path'          => false,
        ]);


        $resolver->setAllowedTypes('save_path', 'bool');
        $resolver->setAllowedTypes('multiple_selection', 'bool');
        $resolver->setAllowedTypes('association', 'bool');
        $resolver->setAllowedTypes('modal_title', 'string');
        $resolver->setAllowedTypes('api_prefix', ['string', 'null']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars = \array_replace($view->vars, [
            'multiple_selection' => $options['multiple_selection'],
            'api_prefix'         => $options['api_prefix'],
            'modal_title'        => $options['modal_title'],
            'association'        => $options['association'],
        ]);
    }


    public function getParent(): string
    {
        return HiddenType::class;
    }


    public function getBlockPrefix(): string
    {
        return 'ranky_media_file_manager';
    }
}

<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Presentation\Form\EasyAdmin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Ranky\MediaBundle\Presentation\Form\RankyMediaFileManagerType;

if (\interface_exists('EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface')) {
    final class EARankyMediaFileManagerField implements FieldInterface
    {
        use FieldTrait;

        public const        OPTION_MULTIPLE_SELECTION = 'multiple_selection';
        public const        OPTION_ASSOCIATION        = 'association';
        public const        OPTION_MODAL_TITLE        = 'modal_title';
        public const        OPTION_SAVE_PATH          = 'save_path';
        public const        OPTION_PREVIEW_JUSTIFICATION = 'preview_justification';
        public const        OPTION_BUTTON_JUSTIFICATION = 'button_justification';

        public static function new(string $propertyName, $label = null): self
        {
            return (new self())
                ->setProperty($propertyName)
                ->setLabel($label)
                ->addWebpackEncoreEntries(
                    Asset::new('ranky_media')
                        ->webpackEntrypointName('ranky_media')
                )
                ->setFormType(RankyMediaFileManagerType::class)
                ->setFormTypeOptions([
                    self::OPTION_SAVE_PATH => false,
                    self::OPTION_MULTIPLE_SELECTION => false,
                    self::OPTION_ASSOCIATION        => false,
                    self::OPTION_MODAL_TITLE        => 'Media File Manager',
                    self::OPTION_PREVIEW_JUSTIFICATION => 'center',
                    self::OPTION_BUTTON_JUSTIFICATION => 'flex-end',
                ])
                ->addFormTheme('@RankyMedia/form.html.twig')
                ->setTemplatePath('@RankyMedia/preview/easyadmin.html.twig')
                ->setSortable(false);
        }

        public function previewJustification(string $justification): self
        {
            $this->setFormTypeOption(self::OPTION_PREVIEW_JUSTIFICATION, $justification);

            return $this;
        }

        public function buttonJustification(string $justification): self
        {
            $this->setFormTypeOption(self::OPTION_BUTTON_JUSTIFICATION, $justification);

            return $this;
        }


        public function multipleSelection(bool $isMultiple = true): self
        {
            $this->setFormTypeOption(self::OPTION_MULTIPLE_SELECTION, $isMultiple);

            return $this;
        }

        public function association(bool $isAssociation = true): self
        {
            $this->setFormTypeOption(self::OPTION_ASSOCIATION, $isAssociation);

            return $this;
        }

        public function modalTitle(string $modalTitle): self
        {
            $this->setFormTypeOption(self::OPTION_MODAL_TITLE, $modalTitle);

            return $this;
        }

        public function savePath(bool $savePath = true): self
        {
            $this->setFormTypeOption(self::OPTION_SAVE_PATH, $savePath);

            return $this;
        }
    }
}

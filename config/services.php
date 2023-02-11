<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ranky\MediaBundle\Application\FileManipulation\GenerateThumbnails\AbstractGenerateImageThumbnails;
use Ranky\MediaBundle\Application\FileManipulation\GenerateThumbnails\GenerateGifImageThumbnails;
use Ranky\MediaBundle\Application\FileManipulation\GenerateThumbnails\GenerateImageThumbnails;
use Ranky\MediaBundle\Domain\Contract\AvailableDatesMediaRepository;
use Ranky\MediaBundle\Domain\Contract\MimeMediaRepository;
use Ranky\MediaBundle\Domain\Service\FileCompressHandler;
use Ranky\MediaBundle\Domain\Service\FileResizeHandler;
use Ranky\MediaBundle\Domain\Service\GenerateThumbnailsHandler;
use Ranky\MediaBundle\Infrastructure\DependencyInjection\MediaBundleExtension;
use Ranky\MediaBundle\Infrastructure\FileManipulation\Compression\SpatieFileCompression;
use Ranky\MediaBundle\Infrastructure\FileManipulation\Resize\FfmpegGifFileResize;
use Ranky\MediaBundle\Infrastructure\FileManipulation\Resize\GifsicleGifFileResize;
use Ranky\MediaBundle\Infrastructure\FileManipulation\Resize\ImagickGifFileResize;
use Ranky\MediaBundle\Infrastructure\FileManipulation\Resize\InterventionFileResize;
use Ranky\MediaBundle\Infrastructure\Persistence\Orm\Repository\DoctrineOrmAvailableDatesMediaRepository;
use Ranky\MediaBundle\Infrastructure\Persistence\Orm\Repository\DoctrineOrmMimeMediaRepository;
use Ranky\MediaBundle\Infrastructure\Validation\UploadedFileValidator;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
        ->defaults()
        ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc
        ->autowire(); // Automatically injects dependencies in your services


    $services
        ->load('Ranky\\MediaBundle\\', '../src/*')
        ->exclude([
            '../src/{DependencyInjection,DQL,Contract,Helper,Common,Entity,Trait,Traits,Migrations,Tests,RankyMediaBundle.php}',
            '../src/**/*Interface.php',
            '../src/Common', // Helpers
            '../src/Infrastructure/DependencyInjection',
            '../src/Domain', // Entity
            '../src/Presentation/Form/EasyAdmin', // EasyAdmin
            '../src/Application/**/*Request.php', // DTO
        ]);

    $services
        ->load('Ranky\\MediaBundle\\Presentation\\Api\\', '../src/Presentation/Api/*')
        ->tag('controller.service_arguments');
    $services
        ->load('Ranky\\MediaBundle\\Presentation\\BackOffice\\', '../src/Presentation/BackOffice/*')
        ->tag('controller.service_arguments');

    // Repositories
    $services->set(DoctrineOrmAvailableDatesMediaRepository::class);
    $services->alias(AvailableDatesMediaRepository::class, DoctrineOrmAvailableDatesMediaRepository::class);

    $services->set(DoctrineOrmMimeMediaRepository::class);
    $services->alias(MimeMediaRepository::class, DoctrineOrmMimeMediaRepository::class);

    // Local File Repository
    /*
        $services->set(LocalFileRepository::class);
        $services->alias(FileRepository::class, LocalFileRepository::class);
    */

    // Upload Validator
    $services->set(UploadedFileValidator::class);

    // Resize
    $services
        ->set(FileResizeHandler::class)
        ->args([tagged_iterator(MediaBundleExtension::TAG_MEDIA_RESIZE)]);
    $services->set(ImagickGifFileResize::class);
    $services->set(FfmpegGifFileResize::class);
    $services->set(GifsicleGifFileResize::class);
    $services->set(InterventionFileResize::class);

    // Generate Thumbnails
    $services
        ->set(GenerateThumbnailsHandler::class)
        ->args([tagged_iterator(MediaBundleExtension::TAG_MEDIA_THUMBNAILS)]);
    $services->set(AbstractGenerateImageThumbnails::class)->abstract();
    $services->set(GenerateImageThumbnails::class)->parent(AbstractGenerateImageThumbnails::class);
    $services->set(GenerateGifImageThumbnails::class)->parent(AbstractGenerateImageThumbnails::class);

    // Compress Thumbnails
    $services
        ->set(FileCompressHandler::class)
        ->args([tagged_iterator(MediaBundleExtension::TAG_MEDIA_COMPRESS)]);
    $services->set(SpatieFileCompression::class);
};

<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\DependencyInjection;

use Ranky\MediaBundle\Application\DataTransformer\MediaToResponseTransformer;
use Ranky\MediaBundle\Application\FileManipulation\CompressFile\CompressFile;
use Ranky\MediaBundle\Application\FileManipulation\Thumbnails\GenerateThumbnails\AbstractGenerateImageThumbnails;
use Ranky\MediaBundle\Domain\Contract\AvailableDatesMediaRepositoryInterface;
use Ranky\MediaBundle\Domain\Contract\FileCompressInterface;
use Ranky\MediaBundle\Domain\Contract\FileResizeInterface;
use Ranky\MediaBundle\Domain\Contract\GenerateThumbnailsInterface;
use Ranky\MediaBundle\Domain\Contract\MediaRepositoryInterface;
use Ranky\MediaBundle\Domain\Contract\MimeMediaRepositoryInterface;
use Ranky\MediaBundle\Domain\Contract\UserMediaRepositoryInterface;
use Ranky\MediaBundle\Domain\Enum\GifResizeDriver;
use Ranky\MediaBundle\Domain\Enum\ImageResizeDriver;
use Ranky\MediaBundle\Infrastructure\FileManipulation\Compression\SpatieFileCompression;
use Ranky\MediaBundle\Infrastructure\FileManipulation\Thumbnails\Resize\FfmpegGifFileResize;
use Ranky\MediaBundle\Infrastructure\FileManipulation\Thumbnails\Resize\GifsicleGifFileResize;
use Ranky\MediaBundle\Infrastructure\FileManipulation\Thumbnails\Resize\ImagickGifFileResize;
use Ranky\MediaBundle\Infrastructure\FileManipulation\Thumbnails\Resize\InterventionFileResize;
use Ranky\MediaBundle\Infrastructure\Filesystem\Local\LocalFilePathResolver;
use Ranky\MediaBundle\Infrastructure\Filesystem\Local\LocalFileUrlResolver;
use Ranky\MediaBundle\Infrastructure\Persistence\Dbal\Types\MediaIdType;
use Ranky\MediaBundle\Infrastructure\Persistence\Dbal\Types\ThumbnailCollectionType;
use Ranky\MediaBundle\Infrastructure\Persistence\Dql\Mysql\MimeSubType;
use Ranky\MediaBundle\Infrastructure\Persistence\Dql\Mysql\MimeType;
use Ranky\MediaBundle\Infrastructure\Persistence\Dql\Mysql\Month;
use Ranky\MediaBundle\Infrastructure\Persistence\Dql\Mysql\Year;
use Ranky\MediaBundle\Infrastructure\Persistence\Orm\Repository\DoctrineOrmAvailableDatesMediaRepository;
use Ranky\MediaBundle\Infrastructure\Persistence\Orm\Repository\DoctrineOrmMediaRepository;
use Ranky\MediaBundle\Infrastructure\Persistence\Orm\Repository\DoctrineOrmMimeMediaRepository;
use Ranky\MediaBundle\Infrastructure\Persistence\Orm\Repository\DoctrineOrmUserMediaRepository;
use Ranky\MediaBundle\Infrastructure\Validation\UploadedFileValidator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class MediaBundleExtension extends Extension implements PrependExtensionInterface
{

    public const CONFIG_DOMAIN_NAME   = 'ranky_media';
    public const TAG_MEDIA_THUMBNAILS = 'ranky.media_thumbnails';
    public const TAG_MEDIA_COMPRESS   = 'ranky.media_compress';
    public const TAG_MEDIA_RESIZE     = 'ranky.media_resize';

    public function getAlias(): string
    {
        return self::CONFIG_DOMAIN_NAME;
    }

    /**
     * @param array<string, mixed> $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @return void
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);
        $container->setParameter(self::CONFIG_DOMAIN_NAME, $config);
        $container->setParameter('ranky_media_api_prefix', $config['api_prefix']);

        // Media
        $container->registerForAutoconfiguration(GenerateThumbnailsInterface::class)
            ->addTag(self::TAG_MEDIA_THUMBNAILS);

        $container->registerForAutoconfiguration(FileCompressInterface::class)
            ->addTag(self::TAG_MEDIA_COMPRESS);

        $container->registerForAutoconfiguration(FileResizeInterface::class)
            ->addTag(self::TAG_MEDIA_RESIZE);

        $phpLoader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../../../config'));
        $phpLoader->load('services.php');

        // check valid configuration
        $this->checkConfiguration($config);

        $mediaCreateDefinition = $container->getDefinition('Ranky\MediaBundle\Application\CreateMedia\CreateMedia');
        $mediaCreateDefinition->setArgument('$mediaEntity', $config['media_entity']);

        /** Another way to define services */
        // Doctrine Media repository
        $doctrineOrmMediaRepositoryDefinition = new Definition(DoctrineOrmMediaRepository::class);
        $doctrineOrmMediaRepositoryDefinition->setAutowired(true);
        $doctrineOrmMediaRepositoryDefinition->setArgument('$mediaEntity', $config['media_entity']);
        $container->setAlias(MediaRepositoryInterface::class, DoctrineOrmMediaRepository::class);
        $container->setDefinition(DoctrineOrmMediaRepository::class, $doctrineOrmMediaRepositoryDefinition);

        $doctrineOrmMimeMediaRepositoryDefinition = new Definition(DoctrineOrmMimeMediaRepository::class);
        $doctrineOrmMimeMediaRepositoryDefinition
            ->setAutowired(true)
            ->setArgument('$mediaEntity', $config['media_entity']);
        $container->setAlias(MimeMediaRepositoryInterface::class, DoctrineOrmMimeMediaRepository::class);
        $container->setDefinition(DoctrineOrmMimeMediaRepository::class, $doctrineOrmMimeMediaRepositoryDefinition);

        $doctrineOrmAvailableDatesMediaRepositoryDefinition = new Definition(
            DoctrineOrmAvailableDatesMediaRepository::class
        );
        $doctrineOrmAvailableDatesMediaRepositoryDefinition
            ->setAutowired(true)
            ->setArgument('$mediaEntity', $config['media_entity']);
        $container->setAlias(
            AvailableDatesMediaRepositoryInterface::class,
            DoctrineOrmAvailableDatesMediaRepository::class
        );
        $container->setDefinition(
            DoctrineOrmAvailableDatesMediaRepository::class,
            $doctrineOrmAvailableDatesMediaRepositoryDefinition
        );


        // Doctrine User Media repository
        $doctrineOrmUserMediaRepositoryDefinition = new Definition(DoctrineOrmUserMediaRepository::class);
        $doctrineOrmUserMediaRepositoryDefinition
            ->setAutowired(true)
            ->setArgument('$userEntity', $config['user_entity'])
            ->setArgument('$userIdentifierProperty', $config['user_identifier_property'])
            ->setArgument('$mediaEntity', $config['media_entity']);

        $container->setAlias(UserMediaRepositoryInterface::class, DoctrineOrmUserMediaRepository::class);
        $container->setDefinition(DoctrineOrmUserMediaRepository::class, $doctrineOrmUserMediaRepositoryDefinition);

        /** Add config parameters to services */
        $uploadUrl = rtrim($config['upload_url'], '/');
        $container->getDefinition(MediaToResponseTransformer::class)
            ->setArgument('$uploadUrl', $uploadUrl)
            ->setArgument('$dateTimeFormat', $config['date_time_format']);

        $container->getDefinition(UploadedFileValidator::class)
            ->setArgument('$mimeTypes', $config['mime_types'])
            ->setArgument('$maxFileSize', $config['max_file_size']);

        $container->getDefinition(LocalFilePathResolver::class)
            ->setArgument('$uploadDirectory', $config['upload_directory']);

        $container->getDefinition(LocalFileUrlResolver::class)
            ->setArgument('$uploadUrl', $uploadUrl);

        $container->getDefinition(CompressFile::class)
            ->setArgument('$disableCompression', $config['disable_compression'])
            ->setArgument('$compressOnlyOriginal', $config['compress_only_original']);

        $container->getDefinition(ImagickGifFileResize::class)
            ->setArgument('$imageResizeGifDriver', $config['image']['resize_gif_driver']);

        $container->getDefinition(FfmpegGifFileResize::class)
            ->setArgument('$imageResizeGifDriver', $config['image']['resize_gif_driver']);

        $container->getDefinition(GifsicleGifFileResize::class)
            ->setArgument('$imageResizeGifDriver', $config['image']['resize_gif_driver']);

        $container->getDefinition(InterventionFileResize::class)
            ->setArgument('$resizeImageDriver', $config['image']['resize_driver']);

        /**
         * We just inject the arguments into the parent
         * @see ../../../config/services.php
         * for the implementations of the GenerateThumbnailsInterface
         */
        $container->getDefinition(AbstractGenerateImageThumbnails::class)
            ->setArgument('$originalMaxWidth', $config['image']['original_max_width'])
            ->setArgument('$breakpoints', $config['image']['breakpoints']);

        $container->getDefinition(SpatieFileCompression::class)
            ->setArgument('$imageQuality', $config['image']['quality']);
    }

    /**
     * @param array<string, mixed> $config
     * @return void
     */
    private function checkConfiguration(array $config): void
    {
        if (\is_string($config['user_entity']) && !\class_exists($config['user_entity'])) {
            throw new \RuntimeException(
                sprintf('The class %s provided for user_entity does not exist', $config['user_entity'])
            );
        }

        if ($config['image']['resize_driver'] === ImageResizeDriver::IMAGICK->value
            && !\extension_loaded('imagick')) {
            throw new \RuntimeException(
                'Imagick extension cannot be used as a driver for image resizing, check it is installed correctly or use GD.'
            );
        }

        if ($config['image']['resize_driver'] === ImageResizeDriver::GD->value
            && !\extension_loaded('gd')) {
            throw new \RuntimeException(
                'GD extension cannot be used as a driver for image resizing, check it is installed correctly or use Imagick.'
            );
        }
        if ($config['image']['resize_driver'] === ImageResizeDriver::GD->value
            && !\extension_loaded('exif')) {
            throw new \RuntimeException(
                'GD requires EXIF extension for Intervention Image package. Check it is installed correctly or use Imagick driver.'
            );
        }

        if ($config['image']['resize_gif_driver'] === GifResizeDriver::FFMPEG->value) {
            $ffmpegCanBeUsed = (bool)@shell_exec('ffmpeg -version');
            if (!$ffmpegCanBeUsed) {
                throw new \RuntimeException(
                    'FFmpeg cannot be used as a driver for gif resizing, check it is installed correctly or use another resize gif driver.'
                );
            }
        }

        if ($config['image']['resize_gif_driver'] === GifResizeDriver::GIFSICLE->value) {
            $gifsicleCanBeUsed = (bool)@shell_exec('gifsicle --version');
            if (!$gifsicleCanBeUsed) {
                throw new \RuntimeException(
                    'Gifsicle cannot be used as a driver for gif resizing, check it is installed correctly or use another resize gif driver.'
                );
            }
        }

        if ($config['image']['resize_gif_driver'] === GifResizeDriver::IMAGICK->value
            && !\extension_loaded('imagick')) {
            throw new \RuntimeException(
                'Imagick cannot be used as a driver for gif resizing, check it is installed correctly or use another resize gif driver.'
            );
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('twig', [
            'form_themes' => [
                '@RankyMedia/form.html.twig',
            ],
            'globals'     => [
                'ranky_media' => '@Ranky\MediaBundle\Application\MediaService',
            ],
        ]);
        $container->prependExtensionConfig('framework', [
            'assets' => [
                'packages' => [
                    'ranky_media' => [
                        'base_path'          => 'bundles/rankymedia/',
                        'json_manifest_path' => '%kernel.project_dir%/public/bundles/rankymedia/manifest.json',
                    ],
                ],
            ],
        ]);
        $container->prependExtensionConfig('webpack_encore', [
            'builds' => [
                'ranky_media' => '%kernel.project_dir%/public/bundles/rankymedia',
            ],
        ]);
        $container->prependExtensionConfig('doctrine', [
            'dbal' => [
                'types' => [
                    'media_id'             => MediaIdType::class,
                    'thumbnail_collection' => ThumbnailCollectionType::class,
                ],
            ],
            'orm'  => [
                'dql' => [
                    'string_functions'   => [
                        'MIME_TYPE'    => MimeType::class,
                        'MIME_SUBTYPE' => MimeSubType::class,
                    ],
                    'datetime_functions' => [
                        'YEAR'  => Year::class,
                        'MONTH' => Month::class,
                    ],
                ],
            ],
        ]);
    }
}

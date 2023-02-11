<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\DependencyInjection;

use Ranky\MediaBundle\Application\DataTransformer\MediaToResponseTransformer;
use Ranky\MediaBundle\Application\FileManipulation\CompressFile\CompressFile;
use Ranky\MediaBundle\Application\FileManipulation\GenerateThumbnails\AbstractGenerateImageThumbnails;
use Ranky\MediaBundle\Application\FileManipulation\WriteFile\WriteTemporaryFileToOrigin;
use Ranky\MediaBundle\Domain\Contract\FileCompress;
use Ranky\MediaBundle\Domain\Contract\FilePathResolver;
use Ranky\MediaBundle\Domain\Contract\FileRepository;
use Ranky\MediaBundle\Domain\Contract\FileResize;
use Ranky\MediaBundle\Domain\Contract\FileUrlResolver;
use Ranky\MediaBundle\Domain\Contract\GenerateThumbnails;
use Ranky\MediaBundle\Domain\Contract\MediaRepository;
use Ranky\MediaBundle\Domain\Contract\TemporaryFileRepository;
use Ranky\MediaBundle\Domain\Contract\UserMediaRepository;
use Ranky\MediaBundle\Domain\Enum\GifResizeDriver;
use Ranky\MediaBundle\Domain\Enum\ImageResizeDriver;
use Ranky\MediaBundle\Infrastructure\FileManipulation\Compression\SpatieFileCompression;
use Ranky\MediaBundle\Infrastructure\FileManipulation\Resize\FfmpegGifFileResize;
use Ranky\MediaBundle\Infrastructure\FileManipulation\Resize\GifsicleGifFileResize;
use Ranky\MediaBundle\Infrastructure\FileManipulation\Resize\ImagickGifFileResize;
use Ranky\MediaBundle\Infrastructure\FileManipulation\Resize\InterventionFileResize;
use Ranky\MediaBundle\Infrastructure\Filesystem\Flysystem\FlysystemFileRepository;
use Ranky\MediaBundle\Infrastructure\Filesystem\Flysystem\FlysystemFileUrlResolver;
use Ranky\MediaBundle\Infrastructure\Filesystem\Local\LocalTemporaryFilePathResolver;
use Ranky\MediaBundle\Infrastructure\Filesystem\Local\LocalTemporaryFileRepository;
use Ranky\MediaBundle\Infrastructure\Persistence\Dbal\Types\MediaIdType;
use Ranky\MediaBundle\Infrastructure\Persistence\Dbal\Types\ThumbnailCollectionType;
use Ranky\MediaBundle\Infrastructure\Persistence\Dql\Mysql\MimeSubType;
use Ranky\MediaBundle\Infrastructure\Persistence\Dql\Mysql\MimeType;
use Ranky\MediaBundle\Infrastructure\Persistence\Dql\Mysql\Month;
use Ranky\MediaBundle\Infrastructure\Persistence\Dql\Mysql\Year;
use Ranky\MediaBundle\Infrastructure\Persistence\Orm\Repository\DoctrineOrmMediaRepository;
use Ranky\MediaBundle\Infrastructure\Persistence\Orm\Repository\DoctrineOrmUserMediaRepository;
use Ranky\MediaBundle\Infrastructure\Validation\UploadedFileValidator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Argument\AbstractArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class MediaBundleExtension extends Extension implements PrependExtensionInterface
{
    public const CONFIG_DOMAIN_NAME   = 'ranky_media';
    public const CONFIG_FILESYSTEM_STORAGE_NAME = 'ranky_media.storage';
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
     * @throws \Exception
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);
        $container->setParameter(self::CONFIG_DOMAIN_NAME, $config);
        $container->setParameter('ranky_media_api_prefix', $config['api_prefix']);

        // Media
        $container->registerForAutoconfiguration(GenerateThumbnails::class)
            ->addTag(self::TAG_MEDIA_THUMBNAILS);

        $container->registerForAutoconfiguration(FileCompress::class)
            ->addTag(self::TAG_MEDIA_COMPRESS);

        $container->registerForAutoconfiguration(FileResize::class)
            ->addTag(self::TAG_MEDIA_RESIZE);

        $phpLoader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../../../config'));
        $phpLoader->load('services.php');

        /* check valid configuration */
        $this->checkConfiguration($config);

        /* set parameters */
        $temporaryDirectory = $config['temporary_directory'];
        $uploadUrl          = \rtrim($config['upload_url'], '/');

        // Flysystem Repository
        $flysystemFileRepositoryDefinition = new Definition(FlysystemFileRepository::class);
        $flysystemFileRepositoryDefinition->setAutowired(true);
        $container->setDefinition(FlysystemFileRepository::class, $flysystemFileRepositoryDefinition);
        $container->setAlias(FileRepository::class, FlysystemFileRepository::class);

        // Flysystem Url Resolver
        $urlResolverDefinition = new Definition(FlysystemFileUrlResolver::class);
        $urlResolverDefinition->setAutowired(true);
        $urlResolverDefinition->setArgument('$uploadUrl', $uploadUrl);
        $urlResolverDefinition->setArgument(
            '$rankyMediaStorageAdapter',
            new AbstractArgument('Defined in MediaCompilerPass')
        );
        $container->setDefinition(FlysystemFileUrlResolver::class, $urlResolverDefinition);
        $container->setAlias(FileUrlResolver::class, FlysystemFileUrlResolver::class);

        // Temporary Repository
        $localTemporaryFileRepository = new Definition(LocalTemporaryFileRepository::class);
        $localTemporaryFileRepository->setAutowired(true);
        $localTemporaryFileRepository->setArgument('$temporaryDirectory', $temporaryDirectory);
        $container->setDefinition(LocalTemporaryFileRepository::class, $localTemporaryFileRepository);
        $container->setAlias(TemporaryFileRepository::class, LocalTemporaryFileRepository::class);

        // Temporary Path Resolver
        $localTemporaryFilePathResolverDefinition = new Definition(LocalTemporaryFilePathResolver::class);
        $localTemporaryFilePathResolverDefinition->setArgument('$temporaryDirectory', $temporaryDirectory);
        $container->setDefinition(LocalTemporaryFilePathResolver::class, $localTemporaryFilePathResolverDefinition);
        $container->setAlias(FilePathResolver::class, LocalTemporaryFilePathResolver::class);

        /* Write File */
        $container->getDefinition(WriteTemporaryFileToOrigin::class)
            ->setArgument('$breakpoints', $config['image']['breakpoints']);

        /* Doctrine Media repository */
        $doctrineOrmMediaRepositoryDefinition = new Definition(DoctrineOrmMediaRepository::class);
        $doctrineOrmMediaRepositoryDefinition->setAutowired(true);
        $container->setAlias(MediaRepository::class, DoctrineOrmMediaRepository::class);
        $container->setDefinition(DoctrineOrmMediaRepository::class, $doctrineOrmMediaRepositoryDefinition);


        /* Doctrine User Media repository */
        $doctrineOrmUserMediaRepositoryDefinition = new Definition(DoctrineOrmUserMediaRepository::class);
        $doctrineOrmUserMediaRepositoryDefinition
            ->setAutowired(true)
            ->setArgument('$userEntity', $config['user_entity'])
            ->setArgument('$userIdentifierProperty', $config['user_identifier_property']);
        $container->setAlias(UserMediaRepository::class, DoctrineOrmUserMediaRepository::class);
        $container->setDefinition(DoctrineOrmUserMediaRepository::class, $doctrineOrmUserMediaRepositoryDefinition);

        /* Add config parameters to services */


        $container->getDefinition(MediaToResponseTransformer::class)
            ->setArgument('$dateTimeFormat', $config['date_time_format']);


        $container->getDefinition(UploadedFileValidator::class)
            ->setArgument('$mimeTypes', $config['mime_types'])
            ->setArgument('$maxFileSize', $config['max_file_size']);

        $container->getDefinition(CompressFile::class)
            ->setArgument('$disableCompression', $config['disable_compression'])
            ->setArgument('$compressOnlyOriginal', $config['compress_only_original'])
            ->setArgument('$breakpoints', $config['image']['breakpoints']);

        $container->getDefinition(ImagickGifFileResize::class)
            ->setArgument('$imageResizeGifDriver', $config['image']['resize_gif_driver']);

        $container->getDefinition(FfmpegGifFileResize::class)
            ->setArgument('$imageResizeGifDriver', $config['image']['resize_gif_driver']);

        $container->getDefinition(GifsicleGifFileResize::class)
            ->setArgument('$imageResizeGifDriver', $config['image']['resize_gif_driver']);

        $container->getDefinition(InterventionFileResize::class)
            ->setArgument('$resizeImageDriver', $config['image']['resize_driver']);

        $container->getDefinition(SpatieFileCompression::class)
            ->setArgument('$imageQuality', $config['image']['quality']);

        /**
         * We just inject the arguments into the parent
         * @see ../../../config/services.php
         * for the implementations of the GenerateThumbnails
         */
        $container->getDefinition(AbstractGenerateImageThumbnails::class)
            ->setArgument('$originalMaxWidth', $config['image']['original_max_width'])
            ->setArgument('$breakpoints', $config['image']['breakpoints']);
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
        $container->prependExtensionConfig('flysystem', [
            'storages' => [
                'ranky_media.storage' => [
                    'adapter'    => 'local',
                    'options'    => [
                        'directory' => Configuration::DEFAULT_UPLOAD_DIRECTORY,
                    ],
                ],
            ],
        ]);

        $container->prependExtensionConfig('twig', [
            'form_themes' => [
                '@RankyMedia/form.html.twig',
            ],
            'globals'     => [
                'ranky_media' => '@Ranky\MediaBundle\Presentation\Twig\MediaTwigService',
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
                'dql'      => [
                    'string_functions'   => [
                        'MIME_TYPE'    => MimeType::class,
                        'MIME_SUBTYPE' => MimeSubType::class,
                    ],
                    'datetime_functions' => [
                        'YEAR'  => Year::class,
                        'MONTH' => Month::class,
                    ],
                ],
                'mappings' => [
                    'RankyMediaBundle' => [
                        'type'   => 'attribute',
                        'dir'    => \dirname(__DIR__, 2).'/Domain',
                        'prefix' => 'Ranky\MediaBundle\Domain',
                    ],
                ],
            ],
        ]);
    }
}

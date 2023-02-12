<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\DependencyInjection;

use Ranky\MediaBundle\Application\DataTransformer\Response\MediaResponse;
use Ranky\MediaBundle\Domain\Criteria\MediaCriteria;
use Ranky\MediaBundle\Domain\Enum\Breakpoint;
use Ranky\MediaBundle\Domain\Enum\GifResizeDriver;
use Ranky\MediaBundle\Domain\Enum\ImageResizeDriver;
use Ranky\MediaBundle\Domain\Model\Media;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const DEFAULT_UPLOAD_DIRECTORY = '%kernel.project_dir%/public/uploads';
    public const DEFAULT_UPLOAD_URL = '/uploads';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(MediaBundleExtension::CONFIG_DOMAIN_NAME);
        $rootNode = $treeBuilder->getRootNode();

        $this->addMainConfigSection($rootNode);
        $this->addMainCompressionConfigSection($rootNode);
        $this->addMainMimeTypesConfigSection($rootNode);
        $this->addImageConfigSection($rootNode);
        $this->addStorageConfigSection($rootNode);

        return $treeBuilder;
    }

    private function addMainConfigSection(ArrayNodeDefinition $rootNode): void
    {
       $rootNode
            ->children()
                ->scalarNode('user_entity')
                    ->info(
                        'This is the fully qualified class name (FQCN) of the user entity class.'.
                        ' This is required in order to get the username in case you are using a different UserIdentifier.'
                    )
                    ->defaultValue(null)
                    ->example('App\Entity\User')
                ->end()
                ->scalarNode('user_identifier_property')
                    ->defaultValue('username')
                    ->cannotBeEmpty()
                    ->info('Required if it is different from username')
                ->end()
                ->scalarNode('locale')
                    ->cannotBeOverwritten()
                    ->defaultValue('%kernel.default_locale%')
                ->end()
                ->scalarNode('date_time_format')
                    ->defaultValue(MediaResponse::DATETIME_FORMAT)
                    ->cannotBeEmpty()
                    ->info('Format for date time. It will be shown in the list view and in the media modal.')
                ->end()
                ->scalarNode('api_prefix')
                    ->defaultValue(null)
                    ->treatFalseLike(null)
                ->end()
                ->integerNode('max_file_size')
                    ->info('Max file size in bytes. Default 7340032 (7 mb)')
                    ->defaultValue(Media::MAX_FILE_SIZE)
                ->end()
                ->integerNode('pagination_limit')
                    ->defaultValue(MediaCriteria::DEFAULT_PAGINATION_LIMIT)
                ->end()
            ->end()
        ;
    }
    private function addMainCompressionConfigSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->booleanNode('disable_compression')
                    ->info('Allow disabling compression, in order to avoid the small overhead compression produces after resizing')
                    ->defaultValue(false)
                ->end()
                ->booleanNode('compress_only_original')
                    ->info(
                        'This will compress only the original image, and thumbnails will be ignored. '.
                                'This can be a good option as the thumbnails are already quite small, '.
                                'and sometimes it may not be necessary to compress them.'
                    )
                    ->defaultValue(false)
                ->end()
            ->end()
        ;
    }

    private function addMainMimeTypesConfigSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                 ->arrayNode('mime_types')->prototype('scalar')->end()
                        ->defaultValue([])
                        ->treatNullLike([])
                        ->treatFalseLike([])
                        ->treatTrueLike([])
                        ->info('Empty array means all mime types are allowed.')
                        ->example("['image/jpeg', 'image/png', 'application/pdf'] or ['image/*'] or ['.jpg', '.png']")
                ->end()
            ->end()
        ;
    }

    private function addImageConfigSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('image')->addDefaultsIfNotSet()
                ->children()
                    ->enumNode('resize_driver')
                        ->values(ImageResizeDriver::drivers())
                        ->defaultValue(ImageResizeDriver::IMAGICK->value)
                    ->end()
                    ->enumNode('resize_gif_driver')
                        ->values(GifResizeDriver::drivers())
                        ->defaultValue(GifResizeDriver::NONE->value)
                    ->end()
                    ->integerNode('quality')
                        ->info('Compression quality for images. Default '.Media::IMAGE_QUALITY)
                        ->defaultValue(Media::IMAGE_QUALITY)
                    ->end()
                    ->scalarNode('original_max_width')
                        ->info(
                            'Maximum width for the original file. This way we will not save a large number of megabytes. '.
                                 'Null value will not resize the original image'
                        )
                        ->defaultValue(Media::ORIGINAL_IMAGE_MAX_WIDTH)
                    ->end()
                    ->arrayNode('breakpoints')->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode(Breakpoint::LARGE->value)->prototype('scalar')->end()
                                ->defaultValue(Breakpoint::LARGE->dimensions())
                            ->end()
                            ->arrayNode(Breakpoint::MEDIUM->value)->prototype('scalar')->end()
                                ->defaultValue(Breakpoint::MEDIUM->dimensions())
                            ->end()
                            ->arrayNode(Breakpoint::SMALL->value)->prototype('scalar')->end()
                                ->defaultValue(Breakpoint::SMALL->dimensions())
                            ->end()
                            ->arrayNode(Breakpoint::XSMALL->value)->prototype('scalar')->end()
                                ->defaultValue(Breakpoint::XSMALL->dimensions())
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addStorageConfigSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
               ->scalarNode('upload_directory')
                    ->cannotBeEmpty()
                    ->example(self::DEFAULT_UPLOAD_DIRECTORY)
                    ->defaultValue(self::DEFAULT_UPLOAD_DIRECTORY)
                ->end()
                ->scalarNode('upload_url')
                    ->cannotBeEmpty()
                    ->example('/uploads | https://mydomain.com/uploads | https://%env(AWS_S3_BUCKET_NAME)%.s3.amazonaws.com')
                    ->defaultValue(self::DEFAULT_UPLOAD_URL)
                ->end()
                ->scalarNode('temporary_directory')
                    ->cannotBeOverwritten()
                    ->defaultValue(\sys_get_temp_dir().'/ranky-media-bundle/')
                ->end()
            ->end();
    }
}

<?php

declare(strict_types=1);

namespace Ranky\MediaBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Ranky\MediaBundle\Infrastructure\DependencyInjection\MediaBundleExtension;
use Ranky\MediaBundle\Infrastructure\DependencyInjection\MediaCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;


class RankyMediaBundle extends Bundle
{

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new MediaCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1);
        if (\class_exists('Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass')) {
            $driver = new Definition(AttributeDriver::class, [[\dirname(__DIR__).'/src/Domain']]);
            $compilerPass = new DoctrineOrmMappingsPass($driver, ['Ranky\MediaBundle\Domain'], []);
            $container->addCompilerPass($compilerPass);
        }
    }

    /**
     * @return \Symfony\Component\DependencyInjection\Extension\ExtensionInterface|null
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new MediaBundleExtension();
        }

        return $this->extension;
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }


}

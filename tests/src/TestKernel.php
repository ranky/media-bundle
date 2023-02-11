<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use FriendsOfBehat\SymfonyExtension\Bundle\FriendsOfBehatSymfonyExtensionBundle;
use League\FlysystemBundle\FlysystemBundle;
use Ranky\MediaBundle\RankyMediaBundle;
use Ranky\SharedBundle\RankySharedBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class TestKernel extends Kernel
{
    use MicroKernelTrait;

    public function __construct(string $environment, bool $debug, private readonly array $extraConfigResources = [])
    {
        parent::__construct($environment, $debug);
    }

    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new SecurityBundle(),
            new FlysystemBundle(),
            new TwigBundle(),
            new RankySharedBundle(),
            new RankyMediaBundle(),
            new FriendsOfBehatSymfonyExtensionBundle(),
        ];
    }

    private function configureContainer(
        ContainerConfigurator $containerConfigurator,
        LoaderInterface $loader,
        ContainerBuilder $builder
    ): void {
        $containerConfigurator->import('../config/services.php');
        //packages
        $containerConfigurator->import('../config/doctrine.php');
        $containerConfigurator->import('../config/framework.php');
        $containerConfigurator->import('../config/security.php');
        $containerConfigurator->import('../config/twig.php');
        // $builder->register('logger', NullLogger::class);

        foreach ($this->extraConfigResources as $resource) {
            $containerConfigurator->import($resource);
        }

        if ($this->extraConfigResources === []) {
            $containerConfigurator->import('../config/flysystem.php');
            $containerConfigurator->import('../config/ranky_media.php');
        }
    }


    protected function configureRoutes(RoutingConfigurator $routingConfigurator): void
    {
        $routingConfigurator->import('@RankyMediaBundle/config/routes.php');
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir().'/ranky_media_bundle_test/cache';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/ranky_media_bundle_test/logs';
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__);
    }
}

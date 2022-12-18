<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use FriendsOfBehat\SymfonyExtension\Bundle\FriendsOfBehatSymfonyExtensionBundle;
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

    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new RankySharedBundle(),
            new RankyMediaBundle(),
            new FriendsOfBehatSymfonyExtensionBundle(),
        ];
    }

    private function configureContainer(
        ContainerConfigurator $container,
        LoaderInterface $loader,
        ContainerBuilder $builder
    ): void {
        $container->import('../config/*.php');
        $container->import('../config/services.php');
        // $builder->register('logger', NullLogger::class);
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('@RankyMediaBundle/config/routes.php');
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

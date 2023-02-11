<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests;

use Doctrine\ORM\EntityManager;
use Ranky\MediaBundle\Infrastructure\DependencyInjection\MediaBundleExtension;
use Ranky\SharedBundle\Common\FileHelper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class BaseIntegrationTestCase extends KernelTestCase
{

    protected function setUp(): void
    {
        self::bootKernel();
    }

    protected static function bootKernelResources(array $extraConfigResources = [], bool $clearCache = false): void
    {
        static::ensureKernelShutdown();

        $kernel = new TestKernel(
            'test',
            (bool) $_ENV['APP_DEBUG'],
            $extraConfigResources
        );
        if ($clearCache){
            FileHelper::removeRecursiveDirectoriesAndFiles($kernel->getCacheDir());
        }
        $kernel->boot();
        static::$kernel = $kernel;
        static::$booted = true;
    }

    protected function container(): ContainerInterface
    {
        return static::getContainer();
    }

    /**
     * @template T of object
     * @param class-string<T> $classOrId
     * @throws \Exception
     * @return T
     */
    protected function getService(string $classOrId): object
    {
        return static::getContainer()->get($classOrId);
    }

    /**
     * @template T of object
     * @param class-string<T> $classOrId
     * @throws \Exception
     * @return T
     */
    protected static function service(string $classOrId): object
    {
        return static::getContainer()->get($classOrId);
    }

    protected function getConfigOption(string $option): mixed
    {
        return $this->container()->getParameter(MediaBundleExtension::CONFIG_DOMAIN_NAME)[$option];
    }

    protected function getDummyDirectory(): string
    {
        return $this->container()->getParameter('kernel.project_dir').'/dummy';
    }

    public function getTempFileDirectory(): string
    {
        $filesDir = \sys_get_temp_dir().'/ranky_media_bundle_test/files';
        FileHelper::makeDirectory($filesDir);

        return $filesDir;
    }

    public function getUploadUrl(): string
    {
        return $_ENV['SITE_URL'].'/uploads';
    }

    public function getTmpUploadDirectory(?string $path = null): string
    {
        $fullPath = \sys_get_temp_dir().'/ranky_media_bundle_test/uploads';
        if ($path) {
            $fullPath .= '/'.ltrim($path, '/');
        }
        return $fullPath;
    }

    protected function getPublicUploadDirectory(): string
    {
        return $this->container()->getParameter('ranky_media_upload_directory');
    }

    /**
     * @throws \Exception
     */
    protected static function getDoctrineManager(): EntityManager
    {
        return self::service('doctrine')->getManager();
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Exception
     */
    protected static function resetTableByClassName(string $className): void
    {
        self::getDoctrineManager()
            ->createQueryBuilder()
            ->from($className, 'className')
            ->delete()
            ->getQuery()
            ->execute();
        self::getDoctrineManager()->flush();
    }

    /**
     * @throws \Doctrine\Persistence\Mapping\MappingException
     * @throws \Exception
     */
    protected static function clearUnitOfWork(): void
    {
        self::getDoctrineManager()->clear();
    }

}

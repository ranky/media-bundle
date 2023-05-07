<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
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

    protected function container(): ContainerInterface
    {
        return static::getContainer();
    }

    /**
     * @template T of object
     * @param class-string<T> $classOrId
     * @return T
     */
    protected function getService(string $classOrId): object
    {
        return static::getContainer()->get($classOrId);
    }

    /**
     * @template T of object
     * @param class-string<T> $classOrId
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

    public function getTempFileDir(): string
    {
        $filesDir = sys_get_temp_dir().'/ranky_media_bundle_test/files';
        FileHelper::makeDirectory($filesDir);

        return $filesDir;
    }

    protected function getDummyDir(): string
    {
        return $this->container()->getParameter('kernel.project_dir').'/dummy';
    }

    protected static function getDoctrineManager(): EntityManagerInterface
    {
        return self::service(EntityManagerInterface::class);
    }

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

    protected static function clearUnitOfWork(): void
    {
        self::getDoctrineManager()->clear();
    }

    public function tearDown():void
    {
        parent::tearDown();
        self::clearUnitOfWork();
    }

}

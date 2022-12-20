<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Tests\Infrastructure\Persistence\Orm\Dql\Sqlite;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use PHPUnit\Framework\TestCase;
use Ranky\MediaBundle\Infrastructure\Persistence\Dql\Sqlite\MimeSubType;
use Ranky\MediaBundle\Infrastructure\Persistence\Dql\Sqlite\MimeType;
use Ranky\MediaBundle\Infrastructure\Persistence\Dql\Sqlite\Month;
use Ranky\MediaBundle\Infrastructure\Persistence\Dql\Sqlite\Year;

class BaseDbSqliteTestCase extends TestCase
{
    /**
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public static function createEntityManager(): EntityManager
    {
        $paths  = [__DIR__.'/../../../../src/Dummy'];
        $config = ORMSetup::createAttributeMetadataConfiguration($paths, true);
        $config->addCustomStringFunction('MIME_TYPE', MimeType::class);
        $config->addCustomStringFunction('MIME_SUBTYPE', MimeSubType::class);
        $config->addCustomStringFunction('YEAR', Year::class);
        $config->addCustomStringFunction('MONTH', Month::class);

        $connection = [
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ];

        return EntityManager::create($connection, $config);
    }
}

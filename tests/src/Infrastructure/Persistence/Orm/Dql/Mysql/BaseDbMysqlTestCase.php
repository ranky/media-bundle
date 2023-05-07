<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Tests\Infrastructure\Persistence\Orm\Dql\Mysql;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use PHPUnit\Framework\TestCase;
use Ranky\MediaBundle\Infrastructure\Persistence\Dql\Mysql\MimeSubType;
use Ranky\MediaBundle\Infrastructure\Persistence\Dql\Mysql\MimeType;
use Ranky\MediaBundle\Infrastructure\Persistence\Dql\Mysql\Month;
use Ranky\MediaBundle\Infrastructure\Persistence\Dql\Mysql\Year;

class BaseDbMysqlTestCase extends TestCase
{

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\Exception\MissingMappingDriverImplementation
     */
    public static function getEntityManager(): EntityManager
    {
        $paths  = [$_SERVER['APP_DIRECTORY'].'/tests/src/Dummy'];
        $config = ORMSetup::createAttributeMetadataConfiguration($paths, true);
        $config->addCustomStringFunction('MIME_TYPE', MimeType::class);
        $config->addCustomStringFunction('MIME_SUBTYPE', MimeSubType::class);
        $config->addCustomStringFunction('YEAR', Year::class);
        $config->addCustomStringFunction('MONTH', Month::class);

        $connection = [
            'url' => getenv('MYSQL_DATABASE_URL'),
        ];

        return new EntityManager(DriverManager::getConnection($connection, $config), $config);
    }
}

<?php

declare(strict_types=1);

use Ranky\MediaBundle\Infrastructure\Persistence\Dql\Mysql\MimeSubType;
use Ranky\MediaBundle\Infrastructure\Persistence\Dql\Mysql\MimeType;
use Ranky\MediaBundle\Infrastructure\Persistence\Dql\Mysql\Month;
use Ranky\MediaBundle\Infrastructure\Persistence\Dql\Mysql\Year;
use Ranky\MediaBundle\Infrastructure\Persistence\Dql\Postgresql\MimeSubType as PostgresqlMimeSubType;
use Ranky\MediaBundle\Infrastructure\Persistence\Dql\Postgresql\MimeType as PostgresqlMimeType;
use Ranky\MediaBundle\Infrastructure\Persistence\Dql\Postgresql\Month as PostgresqlMonth;
use Ranky\MediaBundle\Infrastructure\Persistence\Dql\Postgresql\Year as PostgresqlYear;
use Symfony\Config\DoctrineConfig;

return static function (DoctrineConfig $doctrineConfig): void {
    $dbal = $doctrineConfig->dbal();
    $dbConnection = $_SERVER['DB_CONNECTION'] ?? 'default';

    $dbalConnection = $dbal->connection('default');
    $dbal->defaultConnection('default');
    if ($dbConnection === 'postgres') {
        $dbalConnection
            ->charset('utf8')
            ->url(getenv('POSTGRES_DATABASE_URL'))
        ;
    }else{
        $dbalConnection
            ->charset('utf8mb4')
            ->url(getenv('MYSQL_DATABASE_URL'))
        ;

    }

    $orm = $doctrineConfig->orm();
    $orm->defaultEntityManager('default')->autoGenerateProxyClasses(true);

    $em = $orm->entityManager('default');
    $em->connection('default');
    $em->autoMapping(true);
    $em->namingStrategy('doctrine.orm.naming_strategy.underscore');
    $em->mapping('Tests')
        ->dir('%kernel.project_dir%/src/Dummy')
        ->prefix('Ranky\MediaBundle\Tests\Dummy');
    $dql = $em->dql();

    if ($dbConnection === 'postgres') {
        $dql->stringFunction('MIME_TYPE', PostgresqlMimeType::class);
        $dql->stringFunction('MIME_SUBTYPE', PostgresqlMimeSubType::class);
        $dql->datetimeFunction('YEAR', PostgresqlYear::class);
        $dql->datetimeFunction('MONTH', PostgresqlMonth::class);
    }else{
        $dql->stringFunction('MIME_TYPE', MimeType::class);
        $dql->stringFunction('MIME_SUBTYPE', MimeSubType::class);
        $dql->datetimeFunction('YEAR', Year::class);
        $dql->datetimeFunction('MONTH', Month::class);
    }

};

<?php
declare(strict_types=1);

use Symfony\Config\DoctrineConfig;

return static function (DoctrineConfig $doctrineConfig): void {
    # "TEST_TOKEN" is typically set by ParaTest
    $doctrineConfig->dbal()
        ->defaultConnection('default')
        ->connection('default')
        ->url('%env(resolve:DATABASE_URL)%')
        ->dbnameSuffix('_test%env(default::TEST_TOKEN)%')
        ->logging(false)
        ->charset('utf8')
        ;


    $emDefault = $doctrineConfig->orm()->autoGenerateProxyClasses(true)->entityManager('default');
    $emDefault->autoMapping(true);
    $emDefault
        ->mapping('Tests')
        ->dir('%kernel.project_dir%/src/Dummy')
        ->prefix('Ranky\MediaBundle\Tests\Dummy')
    ;
};

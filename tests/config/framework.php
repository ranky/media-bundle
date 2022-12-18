<?php

declare(strict_types=1);

use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $frameworkConfig): void {

    $frameworkConfig
        ->test(true)
        ->secret('%env(APP_SECRET)%')
        ->enabledLocales(['es','en'])
        ->defaultLocale('es')
        ->httpMethodOverride(false)
    ;

    $frameworkConfig
        ->session()
        ->enabled(true)
        ->storageFactoryId('session.storage.factory.mock_file')
    ;

    $frameworkConfig
        ->router()
        ->utf8(true)
        ->strictRequirements(true)
    ;

    $frameworkConfig
        ->validation()
        ->notCompromisedPassword(['enabled' => false])
    ;
};

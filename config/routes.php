<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {

    $symfonyVersion = Symfony\Component\HttpKernel\Kernel::VERSION;

    if (\version_compare($symfonyVersion, '7.0', '>=')) {
        $routes->import('../src/Presentation/Api', 'attribute');
        $routes->import('../src/Presentation/BackOffice', 'attribute');
    } else {
        $routes->import('../src/Presentation/Api', 'annotation');
        $routes->import('../src/Presentation/BackOffice', 'annotation');
    }
};

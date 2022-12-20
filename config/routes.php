<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {

    $routes->import('../src/Presentation/Api', 'annotation');
    $routes->import('../src/Presentation/BackOffice', 'annotation');
};

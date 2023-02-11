<?php
declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ranky\MediaBundle\Tests\Dummy\User\Infrastructure\DoctrineOrmUserRepository;
use Ranky\MediaBundle\Tests\Dummy\User\Domain\UserRepositoryInterface;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services()
        ->defaults()
        ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc
        ->autowire(); // Automatically injects dependencies in your services

    $configurator->parameters()->set('site_url', '%env(resolve:SITE_URL)%');

  // For AbstractApiContext
  $services
        ->load('Ranky\\MediaBundle\\Tests\\Presentation\\Behat\\', '../src/Presentation/Behat/*');


    // User
    $services
        ->set(DoctrineOrmUserRepository::class)
        ->public();
    $services->alias(UserRepositoryInterface::class, DoctrineOrmUserRepository::class);
};

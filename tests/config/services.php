<?php
declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;


use Ranky\MediaBundle\Tests\Dummy\User\Infrastructure\DoctrineOrmUserRepository;
use Ranky\MediaBundle\Tests\Dummy\User\Domain\UserRepository;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services()
        ->defaults()
        ->public()
        ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc
        ->autowire(); // Automatically injects dependencies in your services

    $configurator->parameters()->set('site_url', '%env(resolve:SITE_URL)%');

  // For KernelInterface in AbstractApiContext
  $services
        ->load('Ranky\\MediaBundle\\Tests\\Presentation\\Behat\\', '../src/Presentation/Behat/*')
        ->exclude([
            '../src/Presentation/Behat/MediaApiContextTrait.php',
        ]);

    $services->load('Ranky\\MediaBundle\\Tests\\DataFixtures\\', '../src/DataFixtures');

    $services
        ->set(DoctrineOrmUserRepository::class)
        ->public();
    $services->alias(UserRepository::class, DoctrineOrmUserRepository::class);
};

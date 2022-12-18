<?php
declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Ranky\MediaBundle\Tests\TestKernel;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Dotenv\Dotenv;


require __DIR__.'/../vendor/autoload.php';

DG\BypassFinals::enable();


(new Dotenv())->bootEnv(__DIR__.'/.env');

// Create and boot 'test' kernel
$kernel = new TestKernel('test', (bool)$_ENV['APP_DEBUG']);
$kernel->boot();

// Create new application console
$application = new Application($kernel);
$application->setAutoExit(false);

$application->run(
    new ArrayInput([
        'command'     => 'cache:clear',
        '--no-warmup' => true,
        '--env'       => 'test',
    ])
);

$application->run(
    new ArrayInput([
        'command'     => 'doctrine:database:drop',
        '--if-exists' => true,
        '--force'     => true,
    ])
);

$application->run(
    new ArrayInput([
        'command' => 'doctrine:database:create',
    ])
);

$application->run(
    new ArrayInput([
        'command' => 'doctrine:schema:update',
        '--force' => true,
    ])
);

$queryFixtures = "INSERT INTO user (username,password,email,roles) VALUES ('jcarlos','password','jcarlos@test.test','[\"ROLE_USER\"]'),('pedro','password','pedro@test.test','[\"ROLE_USER\"]')";
$application->run(new StringInput(sprintf('%s "%s"', 'dbal:run-sql', \addslashes($queryFixtures))));

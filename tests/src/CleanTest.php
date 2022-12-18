<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Tests;

use Ranky\SharedBundle\Common\FileHelper;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

class CleanTest extends BaseIntegrationTestCase
{

    public static function testItShouldRemoveUploadedFiles(): void
    {
        /** @var string[] $configMediaBundle */
        $configMediaBundle = self::getContainer()->getParameter('ranky_media');
        $uploadDirectory   = $configMediaBundle['upload_directory'];
        FileHelper::removeRecursiveDirectoriesAndFiles($uploadDirectory);
        self::assertTrue(true);
    }

    public static function testItShouldRemoveDatabase(): void
    {
        // https://symfonycasts.com/screencast/phpunit-legacy/control-database
        /** @var \Symfony\Component\HttpKernel\KernelInterface $kernel */
        $kernel = self::getContainer()->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $application->run(
            new ArrayInput([
                'command'     => 'doctrine:database:drop',
                '--if-exists' => true,
                '--force'     => true,
            ])
        );

        self::assertTrue(true);
    }

}

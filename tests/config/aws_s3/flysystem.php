<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Aws\S3\S3Client;
use Symfony\Config\FlysystemConfig;

return static function (FlysystemConfig $flysystemConfig, ContainerConfigurator $container): void {
    $flysystemConfig
        ->storage('ranky_media.storage')
        ->adapter('aws')
        ->options([
            'client' => 'aws_client_service',
            'bucket' => '%env(AWS_S3_BUCKET)%',
            'streamReads' => true,
        ]);


    $services = $container->services();
    $services
        ->set('aws_client_service', S3Client::class)
        ->args([
            [
                'region'      => env('AWS_S3_REGION'),
                'version'     => 'latest',
                'credentials' => [
                    'key'    => env('AWS_S3_ACCESS_KEY_ID'),
                    'secret' => env('AWS_S3_SECRET_ACCESS_KEY'),
                ],
            ],
        ]);
};

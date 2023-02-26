# Migration Guide from Version 1 to Version 2 - Ranky Media Bundle

## Breaking Changes

* The bundle now integrates the [Flysystem Bundle](https://github.com/thephpleague/flysystem-bundle), which is maintained by one of the Symfony developers.
* The `uploadDirectory` option is no longer needed, it is now configured in the FlysystemBundle, in case you want a different configuration than the default. 
* The twig function `ranky_media_thumbnail_url` has been deleted
* The twig function `ranky_media_url` now accepts a second argument `$breakpoint` and the `$absolute` argument has been removed
* Although the `ranky_media_url` function is maintained, it is not necessary to use it since MediaResponse object already resolve the media URL. 

This migration guide will walk you through the steps of upgrading to Version 2 of the Ranky Media Bundle.

## An overview of version 1

In Version 1, the local storage came preconfigured, and you only had to change the URL (uploadUrl) and directory (uploadDirectory) for file storage if desired. For example:

```php
// config/packages/ranky_media.php

use Symfony\Config\RankyMediaConfig;

return static function (RankyMediaConfig $rankyMediaConfig) {
 $rankyMediaConfig
        // ...
        ->uploadDirectory('%kernel.project_dir%/public/uploads')
        ->uploadUrl('/uploads')
    ;
}
```

## Version 2

In Version 2, the local storage is still preconfigured, and you do not have to make any changes if you want to use the default configurations. However, if you need to change either the local or remote storage, you will need to make changes in the Flysystem Bundle.

Here is an example for **the local storage**:


>As you can see, the `uploadUrl` configuration is maintained in this manner to prevent Flysystem from making a request to the local or remote file system for each file. This is important because in the case of AWS S3, for example, it would result in a waste of time and potentially incur additional costs if Flysystem were to make a separate request for each file.

```php
// config/packages/ranky_media.php

use Symfony\Config\RankyMediaConfig;

return static function (RankyMediaConfig $rankyMediaConfig) {
 $rankyMediaConfig
          // ...
        ->uploadUrl('/uploads')
    ;
}
```
The storage name `ranky_media.storage` is mandatory.

```php
// config/packages/flysystem.php

use Symfony\Config\FlysystemConfig;

return static function (FlysystemConfig $flysystemConfig): void {
    $flysystemConfig
        ->storage('ranky_media.storage')
        ->adapter('local')
        ->options([
            'directory' => '%kernel.project_dir%/public/uploads',
        ]);
};
```

Here is an example for using AWS S3 as a **remote storage**:

```php
// config/packages/ranky_media.php

use Symfony\Config\RankyMediaConfig;

return static function (RankyMediaConfig $rankyMediaConfig) {
 $rankyMediaConfig
          // ...
        ->uploadUrl('%env(AWS_S3_UPLOAD_URL)%')
        // or
        ->uploadUrl('https://example.s3.amazonaws.com')
    ;
}
```
And here is an example that also includes the configuration for the AWS service:

```php
// config/packages/flysystem.php

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
```
Yaml configuration example:

```yaml
# config/packages/flysystem.yaml

flysystem:
  storages:
    ranky_media.storage:
      adapter: aws
      options:
        client: 'aws_client_service'
        bucket: '%env(AWS_S3_BUCKET)%'
        streamReads: true

services:
  aws_client_service:
    class: Aws\S3\S3Client
    arguments:
      -
        region: '%env(AWS_S3_REGION)%'
        version: 'latest'
        credentials:
          key: '%env(AWS_S3_ACCESS_KEY_ID)%'
          secret: '%env(AWS_S3_SECRET_ACCESS_KEY)%'
```

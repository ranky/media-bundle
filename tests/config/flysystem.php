<?php

declare(strict_types=1);


use Symfony\Config\FlysystemConfig;

return static function (FlysystemConfig $flysystemConfig): void {
    $flysystemConfig
        ->storage('ranky_media.storage')
        ->adapter('local')
        ->options([
            'directory' => '%kernel.project_dir%/public/uploads',
        ]);
};

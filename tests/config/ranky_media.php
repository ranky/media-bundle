<?php
declare(strict_types=1);

use Ranky\MediaBundle\Tests\Dummy\Media\Domain\Media;
use Ranky\MediaBundle\Tests\Dummy\User\Domain\User;
use Symfony\Config\RankyMediaConfig;

return static function (RankyMediaConfig $rankyMediaConfig): void {

    $rankyMediaConfig
        ->mediaEntity(Media::class)
        ->userEntity(User::class)
        ->userIdentifierProperty('username')
        ->paginationLimit(30)
    ;
};

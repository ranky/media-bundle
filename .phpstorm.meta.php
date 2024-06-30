<?php

namespace PHPSTORM_META {
    override(\Interop\Container\ContainerInterface::get(0), map([
        '' => '@',
        'ranky_media' => \Ranky\MediaBundle\Application\MediaService::class,
    ]));
};


<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Infrastructure\DependencyInjection;


use Doctrine\ORM\Events;
use Ranky\MediaBundle\Domain\Model\Media;
use Ranky\MediaBundle\Domain\Model\MediaInterface;
use Ranky\SharedBundle\Filter\Attributes\CriteriaValueResolver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;


class MediaCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container): void
    {
        $twigGlobal = $container->getDefinition('twig');
        $twigGlobal->addMethodCall(
            'addGlobal',
            [
                'ranky_media_api_prefix',
                $container->getParameter('ranky_media_api_prefix') ?? '',
            ]
        );

        /**
         * TODO: Criteria Value Resolver
         * Problem: $paginationLimit
         * Alternatives:
         *  * Maintain as is, but in the other cases of use the CriteriaValueResolver,
         *    I will have to explicitly use the paginationLimit in the PHP Criteria Attribute
         * @see \Ranky\MediaBundle\Presentation\Api\ListMediaCriteriaApiController::__invoke()
         *  * Pass limit only by url query string from frontend or PHP Attribute
         *  * Duplicate CriteriaValueResolver by MediaCriteriaValueResolver
         *  * https://symfony.com/doc/current/service_container.html#explicitly-configuring-services-and-arguments
         *  * ...
         */
        $criteriaValueResolverDefinition = $container->getDefinition(CriteriaValueResolver::class);
        $mediaConfig                     = $container->getParameter('ranky_media');
        /** @var array<string, mixed> $mediaConfig */
        $criteriaValueResolverDefinition->setArgument('$paginationLimit', $mediaConfig['pagination_limit']);
        /**
         * Resolver target entities
         */
        $resolverTargetEntities = $container->findDefinition('doctrine.orm.listeners.resolve_target_entity');
        $resolverTargetEntities->addMethodCall(
            'addResolveTargetEntity',
            [MediaInterface::class, $mediaConfig['media_entity'], []]
        );
        $resolverTargetEntities->addMethodCall(
            'addResolveTargetEntity',
            [Media::class, $mediaConfig['media_entity'], []]
        );
        $resolverTargetEntities
            ->addTag('doctrine.event_listener', ['event' => Events::loadClassMetadata])
            ->addTag('doctrine.event_listener', ['event' => Events::onClassMetadataNotFound]);
    }

}

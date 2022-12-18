<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Presentation\Api;


use Psr\Container\ContainerInterface;
use Ranky\MediaBundle\Infrastructure\DependencyInjection\MediaBundleExtension;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class BaseMediaApiController implements ServiceSubscriberInterface
{

    public ContainerInterface $container;

    #[Required]
    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }


    public static function getSubscribedServices(): array
    {
        return [
            'translator' => '?'.TranslatorInterface::class,
            //'serializer' => '?Symfony\Component\Serializer\SerializerInterface',
        ];
    }

    /**
     * @param mixed $data
     * @param int $status
     * @param string[] $headers
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function json(mixed $data, int $status = 200, array $headers = []): JsonResponse
    {
        return new JsonResponse($data, $status, $headers);
    }

    /**
     * @param string $id
     * @param array<string, mixed> $parameters
     * @param string $domain
     * @param string|null $locale
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @return string
     */
    protected function trans(
        string $id,
        array $parameters = [],
        string $domain = MediaBundleExtension::CONFIG_DOMAIN_NAME,
        string $locale = null
    ): string {
        return $this->container->get('translator')->trans($id, $parameters, $domain, $locale);
    }
}

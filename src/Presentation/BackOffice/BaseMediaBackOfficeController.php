<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Presentation\BackOffice;


use Psr\Container\ContainerInterface;
use Ranky\MediaBundle\Infrastructure\DependencyInjection\MediaBundleExtension;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

abstract class BaseMediaBackOfficeController implements ServiceSubscriberInterface
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
            'twig'       => '?'.Environment::class,
        ];
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

    /**
     * @param string $view
     * @param array<string, mixed> $parameters
     * @param \Symfony\Component\HttpFoundation\Response|null $response
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function render(string $view, array $parameters = [], Response $response = null): Response
    {
        if (null === $response) {
            $response = new Response();
        }

        $response->setContent($this->renderView($view, $parameters));

        return $response;
    }

    /**
     * @param string $view
     * @param array<string, mixed> $parameters
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @return string
     */
    protected function renderView(string $view, array $parameters = []): string
    {
        return $this->container->get('twig')->render($view, $parameters);
    }
}

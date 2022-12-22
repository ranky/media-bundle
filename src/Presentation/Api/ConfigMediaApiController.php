<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Presentation\Api;

use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Infrastructure\DependencyInjection\MediaBundleExtension;
use Ranky\SharedBundle\Domain\Exception\ApiProblem\ApiProblemException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;


#[Route('/ranky/media/config', name: 'ranky_media_config', methods: ['GET'], priority: 3)]
class ConfigMediaApiController extends BaseMediaApiController
{

    /**
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @param TranslatorInterface&TranslatorBagInterface $translator
     *
     * @return JsonResponse
     */
    public function __invoke(
        Request $request,
        ParameterBagInterface $parameterBag,
        TranslatorInterface $translator
    ): JsonResponse {
        try {
            /** @var array<string, mixed> $config */
            $config = $parameterBag->get(MediaBundleExtension::CONFIG_DOMAIN_NAME);
            /** @var array<string, string> $translations */
            $translations = $translator->getCatalogue()->all(MediaBundleExtension::CONFIG_DOMAIN_NAME);

            return $this->json([
                'config'       => [
                    'pagination_limit'        => $config['pagination_limit'],
                    'upload_url'              => $config['upload_url'],
                    'max_file_size'           => $config['max_file_size'],
                    'mime_types'              => $config['mime_types'],
                    'locale'                  => $config['locale'],
                    'supported_image_types'   => MimeType::supportedImageTypes(),
                    'placeholder_image_types' => MimeType::imagesTypesWithPlaceholder(),
                ],
                'translations' => $translations,
            ]);
        } catch (\Throwable $throwable) {
            throw ApiProblemException::fromThrowable($throwable);
        }
    }

}

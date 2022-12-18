<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Presentation\Api;


use Ranky\MediaBundle\Application\GetMedia\GetMediaById;
use Ranky\SharedBundle\Domain\Exception\ApiProblem\ApiProblemException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


#[Route(
    '/ranky/media/{id}',
    name: 'ranky_media_get_by_id',
    requirements: ['id' => '[0-7][0-9A-HJKMNP-TV-Z]{25}'],
    methods: ['GET'],
    priority: 2
)]
class GetMediaByIdApiController extends BaseMediaApiController
{

    public function __construct(private readonly GetMediaById $showMedia)
    {
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(Request $request, string $id): JsonResponse
    {
        try {
            $mediaResponse = $this->showMedia->__invoke($request->attributes->get('id'));
        } catch (\Throwable $throwable) {
            throw ApiProblemException::fromThrowable($throwable);
        }

        return $this->json($mediaResponse);
    }

}

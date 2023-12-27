<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Presentation\Api;


use Ranky\MediaBundle\Application\UpdateMedia\UpdateMedia;
use Ranky\MediaBundle\Application\UpdateMedia\UpdateMediaRequest;
use Ranky\MediaBundle\Infrastructure\Event\PostUpdateEvent;
use Ranky\MediaBundle\Infrastructure\Event\PreUpdateEvent;
use Ranky\MediaBundle\Infrastructure\Validation\UpdateMediaConstraint;
use Ranky\SharedBundle\Domain\Exception\ApiProblem\ApiProblemException;
use Ranky\SharedBundle\Presentation\Attributes\Body\Body;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;


#[Route('/ranky/media/{id}', name: 'ranky_media_update', requirements: ['id' => '[0-7][0-9A-HJKMNP-TV-Z]{25}'], methods: ['PUT'], priority: 2)]
class UpdateMediaApiController extends BaseMediaApiController
{

    public function __construct(
        private readonly UpdateMedia $updateMedia,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(
        #[CurrentUser]
        ?UserInterface $user,
        #[Body(constraint: UpdateMediaConstraint::class)]
        UpdateMediaRequest $updateMediaRequest,
        string $id = null
    ): JsonResponse {
        if (null === $id) {
            throw ApiProblemException::create(
                $this->trans('errors.bad_request', ['field' => 'id'])
            );
        }

        try {
            $this->eventDispatcher->dispatch(
                new PreUpdateEvent($updateMediaRequest),
                PreUpdateEvent::NAME
            );
            $mediaResponse = $this->updateMedia->__invoke(
                $updateMediaRequest,
                $user?->getUserIdentifier()
            );
            $this->eventDispatcher->dispatch(
                new PostUpdateEvent($mediaResponse),
                PostUpdateEvent::NAME
            );
        } catch (\Throwable $throwable) {
            throw ApiProblemException::fromThrowable($throwable);
        }

        return $this->json($mediaResponse, Response::HTTP_OK);
    }

}

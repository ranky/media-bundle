<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Presentation\Api;


use Ranky\MediaBundle\Application\CreateMedia\CreateMedia;
use Ranky\MediaBundle\Application\CreateMedia\UploadedFileRequest;
use Ranky\MediaBundle\Infrastructure\Event\PostCreateEvent;
use Ranky\MediaBundle\Infrastructure\Event\PreCreateEvent;
use Ranky\MediaBundle\Infrastructure\Validation\UploadedFileValidator;
use Ranky\SharedBundle\Presentation\Attributes\File\File;
use Ranky\SharedBundle\Domain\Exception\ApiProblem\ApiProblemException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;


#[Route('/ranky/media', name: 'ranky_media_upload', methods: ['POST'], priority: 3)]
class UploadMediaApiController extends BaseMediaApiController
{

    public function __construct(
        private readonly CreateMedia $createMedia,
        private readonly UploadedFileValidator $uploadedFileValidator,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }


    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(
        #[CurrentUser] ?UserInterface $user,
        #[File] UploadedFileRequest $uploadedFileRequest = null
    ): JsonResponse {
        if (null === $uploadedFileRequest) {
            throw ApiProblemException::create($this->trans('errors.not_files'));
        }

        try {
            $this->uploadedFileValidator->validate($uploadedFileRequest);
            $this->eventDispatcher->dispatch(
                new PreCreateEvent($uploadedFileRequest),
                PreCreateEvent::NAME
            );
            $mediaResponse = $this->createMedia->__invoke(
                $uploadedFileRequest,
                $user?->getUserIdentifier()
            );
            $this->eventDispatcher->dispatch(
                new PostCreateEvent($mediaResponse),
                PostCreateEvent::NAME
            );
        } catch (\Throwable $throwable) {
            throw ApiProblemException::fromThrowable($throwable);
        }

        return $this->json($mediaResponse, Response::HTTP_OK);
    }

}

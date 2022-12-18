<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Application\UpdateMedia;

use Ranky\MediaBundle\Application\DataTransformer\MediaToResponseTransformer;
use Ranky\MediaBundle\Application\DataTransformer\Response\MediaResponse;
use Ranky\MediaBundle\Application\FileManipulation\RenameFile\RenameFile;
use Ranky\MediaBundle\Domain\Contract\MediaRepositoryInterface;
use Ranky\MediaBundle\Domain\ValueObject\Description;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;
use Ranky\SharedBundle\Domain\Event\DomainEventPublisher;
use Ranky\SharedBundle\Domain\ValueObject\UserIdentifier;

class UpdateMedia
{

    public function __construct(
        private readonly MediaRepositoryInterface $mediaRepository,
        private readonly MediaToResponseTransformer $responseTransformer,
        private readonly RenameFile $renameFile,
        private readonly DomainEventPublisher $domainEventPublisher
    ) {
    }


    public function __invoke(UpdateMediaRequest $updateMediaRequest, ?string $userIdentifier): MediaResponse
    {
        $this->renameFile->__invoke($updateMediaRequest, $userIdentifier);

        $media            = $this->mediaRepository->getById(MediaId::fromString($updateMediaRequest->id()));
        $userIdentifierVO = new UserIdentifier($userIdentifier);
        $description      = new Description($updateMediaRequest->alt(), $updateMediaRequest->title());

        $media->updateDescription($description, $userIdentifierVO);
        $this->mediaRepository->save($media);
        $this->domainEventPublisher->publish(...$media->recordedEvents());

        return $this->responseTransformer->mediaToResponse($media);
    }


}

<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Presentation\Form\DataTransformer;

use Ranky\MediaBundle\Domain\Contract\MediaRepositoryInterface;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;
use Symfony\Component\Form\DataTransformerInterface;

class MediaPathTransformer implements DataTransformerInterface
{
    public function __construct(
        private readonly MediaRepositoryInterface $mediaRepository,
        private readonly bool $multipleSelection = false,
    ) {
    }

    public function transform(mixed $value): ?string
    {
        if (!$value) {
            return null;
        }
        if ($this->multipleSelection) {
            $medias   = $this->mediaRepository->findByFileNames($value);
            $mediaIds = \array_map(static fn($media) => $media->id()->asString(), $medias);
            return \json_encode($mediaIds, \JSON_THROW_ON_ERROR);
        }

        return $this->mediaRepository->findByFileName($value)?->id()->asString();
    }

    public function reverseTransform(mixed $value): string|array|null
    {
        if (null === $value || '' === $value) {
            return null;
        }

        try {
            if ($this->multipleSelection) {
                $ids    = \json_decode($value, true, 512, \JSON_THROW_ON_ERROR);
                $medias = $this->mediaRepository->findByIds(
                    ...\array_map(static fn(string $id) => MediaId::fromString($id), $ids)
                );

                return \array_map(static fn($media) => $media->file()->path(), $medias);
            }

            $media = $this->mediaRepository->getById(MediaId::fromString($value));

            return $media->file()->path();
        } catch (\Throwable) {
            return '';
        }
    }
}

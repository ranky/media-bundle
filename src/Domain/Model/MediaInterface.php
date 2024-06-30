<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Domain\Model;

use Ranky\MediaBundle\Domain\ValueObject\Description;
use Ranky\MediaBundle\Domain\ValueObject\Dimension;
use Ranky\MediaBundle\Domain\ValueObject\File;
use Ranky\MediaBundle\Domain\ValueObject\MediaId;
use Ranky\MediaBundle\Domain\ValueObject\Thumbnails;
use Ranky\SharedBundle\Domain\Event\DomainEvent;
use Ranky\SharedBundle\Domain\ValueObject\UserIdentifier;

interface MediaInterface
{
    public function id(): MediaId;

    public function file(): File;

    public function dimension(): Dimension;

    public function description(): Description;

    public function thumbnails(): Thumbnails;

    public function createdBy(): UserIdentifier;

    public function updatedBy(): UserIdentifier;

    public function createdAt(): \DateTimeImmutable;

    public function updatedAt(): \DateTimeImmutable;

    public static function create(
        MediaId $id,
        File $file,
        UserIdentifier $userIdentifier,
        Dimension $dimension,
        Description $description
    ): self;

    public function addThumbnails(Thumbnails $thumbnails): void;

    public function updateThumbnails(Thumbnails $thumbnails): void;

    public function updateFileSize(int $size): void;

    public function updateFileDimension(File $file, Dimension $dimension): void;

    public function updateDescription(Description $description, UserIdentifier $userIdentifier): void;

    public function updateFile(File $file, UserIdentifier $userIdentifier): void;

    public function delete(): void;

    /**
     * @return DomainEvent[]
     */
    public function recordedEvents(): array;

}

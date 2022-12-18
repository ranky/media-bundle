<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Tests\Presentation\Api\Event;

use Ranky\MediaBundle\Application\CreateMedia\CreateMedia;
use Ranky\MediaBundle\Application\CreateMedia\UploadedFileRequest;
use Ranky\MediaBundle\Application\DataTransformer\Response\MediaResponse;
use Ranky\MediaBundle\Application\DeleteMedia\DeleteMedia;
use Ranky\MediaBundle\Application\UpdateMedia\UpdateMedia;
use Ranky\MediaBundle\Application\UpdateMedia\UpdateMediaRequest;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Domain\Model\Media;
use Ranky\MediaBundle\Infrastructure\Event\DeleteEvent;
use Ranky\MediaBundle\Infrastructure\Event\PostCreateEvent;
use Ranky\MediaBundle\Infrastructure\Event\PostUpdateEvent;
use Ranky\MediaBundle\Infrastructure\Event\PreCreateEvent;
use Ranky\MediaBundle\Infrastructure\Event\PreUpdateEvent;
use Ranky\MediaBundle\Infrastructure\Validation\UploadedFileValidator;
use Ranky\MediaBundle\Presentation\Api\DeleteMediaApiController;
use Ranky\MediaBundle\Presentation\Api\UpdateMediaApiController;
use Ranky\MediaBundle\Presentation\Api\UploadMediaApiController;
use Ranky\MediaBundle\Tests\BaseIntegrationTestCase;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventSubscriberControllerTest extends BaseIntegrationTestCase
{
    private Media $media;

    protected function setUp(): void
    {
        parent::setUp();
        $this->media = MediaFactory::random(MimeType::IMAGE, 'png');
    }


    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testItShouldDispatchPreAndPostCreateEventSubscribers(): void
    {
        $uploadedFileRequest = new UploadedFileRequest(
            $this->media->file()->path(),
            $this->media->file()->name(),
            $this->media->file()->mime(),
            $this->media->file()->extension(),
            $this->media->file()->size()
        );
        $mediaResponse       = MediaResponse::fromMedia(
            $this->media,
            '/uploads',
            $this->media->createdBy()->value(),
            $this->media->createdBy()->value()
        );

        $createMedia = $this->getMockBuilder(CreateMedia::class)->disableOriginalConstructor()->getMock();
        $createMedia
            ->expects($this->once())
            ->method('__invoke')
            ->with($uploadedFileRequest, null)
            ->willReturn($mediaResponse);

        $uploadedFileValidator = $this->createMock(UploadedFileValidator::class);
        $uploadedFileValidator
            ->expects($this->once())
            ->method('validate')
            ->with($uploadedFileRequest);

        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher               = static::getContainer()->get(EventDispatcherInterface::class);
        $assertPreCreateEventCallback  = function (PreCreateEvent $event) use ($uploadedFileRequest): void {
            $this->assertSame($uploadedFileRequest, $event->getUploadedFileRequest());
        };
        $assertPostCreateEventCallback = function (PostCreateEvent $event) use ($mediaResponse): void {
            $this->assertSame($mediaResponse, $event->getMediaResponse());
        };
        $preCreatedEvent               = new class($assertPreCreateEventCallback, $assertPostCreateEventCallback) implements
            EventSubscriberInterface {
            public function __construct(
                private readonly \Closure $assertPreCreateEventCallback,
                private readonly \Closure $assertPostCreateEventCallback
            ) {
            }

            public static function getSubscribedEvents(): array
            {
                return [
                    PreCreateEvent::NAME => 'onPreCreateEvent',
                    PostCreateEvent::NAME => 'onPostCreateEvent',
                ];
            }

            public function onPreCreateEvent(PreCreateEvent $event): void
            {
                ($this->assertPreCreateEventCallback)($event);
            }

            public function onPostCreateEvent(PostCreateEvent $event): void
            {
                ($this->assertPostCreateEventCallback)($event);
            }
        };
        $eventDispatcher->addSubscriber($preCreatedEvent);

        $uploadMediaApiController = new UploadMediaApiController(
            $createMedia,
            $uploadedFileValidator,
            $eventDispatcher
        );

        $uploadMediaApiController->setContainer(static::getContainer());

        $response = $uploadMediaApiController->__invoke(null, $uploadedFileRequest);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testItShouldDispatchPreAndPostUpdateEventSubscribers(): void
    {
        $updateMediaRequest = new UpdateMediaRequest(
            $this->media->id()->asString(),
            $this->media->file()->name(),
            $this->media->description()->alt(),
            $this->media->description()->title(),
        );
        $mediaResponse      = MediaResponse::fromMedia(
            $this->media,
            '/uploads',
            $this->media->createdBy()->value(),
            $this->media->createdBy()->value()
        );

        $updateMedia = $this->getMockBuilder(UpdateMedia::class)->disableOriginalConstructor()->getMock();
        $updateMedia
            ->expects($this->once())
            ->method('__invoke')
            ->with($updateMediaRequest, null)
            ->willReturn($mediaResponse);


        $assertPreUpdateEventCallback  = function (PreUpdateEvent $event) use ($updateMediaRequest): void {
            $this->assertSame($updateMediaRequest, $event->getUpdateMediaRequest());
        };
        $assertPostUpdateEventCallback = function (PostUpdateEvent $event) use ($mediaResponse): void {
            $this->assertSame($mediaResponse, $event->getMediaResponse());
        };
        $preCreatedEvent               = new class($assertPreUpdateEventCallback, $assertPostUpdateEventCallback) implements
            EventSubscriberInterface {
            public function __construct(
                private readonly \Closure $assertPreUpdateEventCallback,
                private readonly \Closure $assertPostUpdateEventCallback
            ) {
            }

            public static function getSubscribedEvents(): array
            {
                return [
                    PreUpdateEvent::NAME => 'onPreUpdateEvent',
                    PostUpdateEvent::NAME => 'onPostUpdateEvent',
                ];
            }

            public function onPreUpdateEvent(PreUpdateEvent $event): void
            {
                ($this->assertPreUpdateEventCallback)($event);
            }

            public function onPostUpdateEvent(PostUpdateEvent $event): void
            {
                ($this->assertPostUpdateEventCallback)($event);
            }
        };

        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = static::getContainer()->get(EventDispatcherInterface::class);
        $eventDispatcher->addSubscriber($preCreatedEvent);
        $updateMediaController = new UpdateMediaApiController(
            $updateMedia,
            $eventDispatcher
        );

        $updateMediaController->setContainer(static::getContainer());

        $response = $updateMediaController->__invoke(null, $updateMediaRequest, $this->media->id()->asString());

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Throwable
     */
    public function testItShouldDispatchPreAndPostDeleteEventSubscribers(): void
    {
        $mediaId     = $this->media->id();
        $deleteMedia = $this->getMockBuilder(DeleteMedia::class)->disableOriginalConstructor()->getMock();
        $deleteMedia
            ->expects($this->once())
            ->method('__invoke')
            ->with($mediaId->asString());


        $assertPreDeleteEventCallback  = function (DeleteEvent $event) use ($mediaId): void {
            $this->assertEquals($mediaId, $event->getMediaId());
        };
        $assertPostDeleteEventCallback = function (DeleteEvent $event) use ($mediaId): void {
            $this->assertEquals($mediaId, $event->getMediaId());
        };
        $preCreatedEvent               = new class($assertPreDeleteEventCallback, $assertPostDeleteEventCallback) implements
            EventSubscriberInterface {
            public function __construct(
                private readonly \Closure $assertPreDeleteEventCallback,
                private readonly \Closure $assertPostDeleteEventCallback
            ) {
            }

            public static function getSubscribedEvents(): array
            {
                return [
                    DeleteEvent::PRE_DELETE => 'onPreDeleteEvent',
                    DeleteEvent::POST_DELETE => 'onPostDeleteEvent',
                ];
            }

            public function onPreDeleteEvent(DeleteEvent $event): void
            {
                ($this->assertPreDeleteEventCallback)($event);
            }

            public function onPostDeleteEvent(DeleteEvent $event): void
            {
                ($this->assertPostDeleteEventCallback)($event);
            }
        };

        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = static::getContainer()->get(EventDispatcherInterface::class);
        $eventDispatcher->addSubscriber($preCreatedEvent);

        $deleteMediaController = new DeleteMediaApiController(
            $deleteMedia,
            $eventDispatcher
        );
        $deleteMediaController->setContainer(static::getContainer());

        $response = $deleteMediaController->__invoke($mediaId->asString());

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

}

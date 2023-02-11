<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Presentation\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Ranky\MediaBundle\Domain\Contract\MediaRepository;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Domain\Model\Media;
use Ranky\MediaBundle\Presentation\Form\RankyMediaFileManagerType;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class RankyMediaFileManagerTypeWithoutDataClassTest extends TypeTestCase
{
    private MediaRepository $mediaRepository;
    private Media $media;

    protected function setUp(): void
    {
        $media                 = MediaFactory::random(MimeType::IMAGE, 'png');
        $this->media           = $media;
        $this->mediaRepository = $this->createMock(MediaRepository::class);
        $this->mediaRepository
            ->method('getById')
            ->with($this->media->id())
            ->willReturn($this->media);
        $this->mediaRepository
            ->method('findByIds')
            ->with($this->media->id())
            ->willReturn([$this->media]);

        parent::setUp();
    }

    /**
     * @return \Symfony\Component\Form\PreloadedExtension[]
     */
    protected function getExtensions(): array
    {
        // create a type instance with the mocked dependencies
        $type = new RankyMediaFileManagerType($this->mediaRepository);

        return [
            // register the type instances with the PreloadedExtension
            new PreloadedExtension([$type], []),
        ];
    }

    public function testItOneSelectionWithMediaIdTypeAndNoAssociationSubmit(): void
    {
        $form = $this->factory->create(RankyMediaFileManagerType::class, null, [
            'data_class' => null,
        ]);
        $form->submit($this->media->id()->asString());
        // This check ensures there are no transformation failures
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($this->media->id(), $form->getData());
    }


    /**
     * @throws \JsonException
     */
    public function testItMultipleSelectionWithJsonTypeAndNoAssociationSubmit(): void
    {
        $data = [$this->media->id()->asString()];
        $form = $this->factory->create(RankyMediaFileManagerType::class, null, [
            'multiple_selection' => true,
            'data_class'         => null,
        ]);
        $form->submit(\json_encode($data, JSON_THROW_ON_ERROR));
        // This check ensures there are no transformation failures
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($data, $form->getData());
    }

    public function testItOneSelectionWithManyToOneTypeAndAssociationSubmit(): void
    {
        $form = $this->factory->create(RankyMediaFileManagerType::class, null, [
            'data_class'  => null,
            'association' => true,
        ]);
        $form->submit($this->media->id()->asString());
        // This check ensures there are no transformation failures
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($this->media, $form->getData());
    }

    public function testItMultipleSelectionWithManyToManyTypeWithJoinTableAndAssociationSubmit(): void
    {
        $form = $this->factory->create(RankyMediaFileManagerType::class, null, [
            'data_class'         => null,
            'association'        => true,
            'multiple_selection' => true,
        ]);
        $form->submit(\json_encode([$this->media->id()->asString()], JSON_THROW_ON_ERROR));
        // This check ensures there are no transformation failures
        $this->assertTrue($form->isSynchronized());

        $arrayCollection = new ArrayCollection();
        $arrayCollection->add($this->media);
        $this->assertEquals($arrayCollection, $form->getData());
    }

}

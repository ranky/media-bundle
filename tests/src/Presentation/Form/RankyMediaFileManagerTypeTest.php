<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Presentation\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Ranky\MediaBundle\Domain\Contract\MediaRepositoryInterface;
use Ranky\MediaBundle\Domain\Enum\MimeType;
use Ranky\MediaBundle\Domain\Model\Media;
use Ranky\MediaBundle\Presentation\Form\RankyMediaFileManagerType;
use Ranky\MediaBundle\Tests\Domain\MediaFactory;
use Ranky\MediaBundle\Tests\Dummy\Page\Domain\Page;
use Ranky\MediaBundle\Tests\Dummy\Page\Presentation\Form\PageType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class RankyMediaFileManagerTypeTest extends TypeTestCase
{
    private MediaRepositoryInterface $mediaRepository;
    private Media $media;

    protected function setUp(): void
    {
        $media                 = MediaFactory::random(MimeType::IMAGE, 'jpg');
        $this->media           = $media;
        $this->mediaRepository = $this->createMock(MediaRepositoryInterface::class);
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

    /**
     * @throws \JsonException
     */
    public function testItSubmitValidData(): void
    {
        $formData     = [
            'mediaId' => $this->media->id()->asString(),
            'gallery' => \json_encode([$this->media->id()->asString()], JSON_THROW_ON_ERROR),
            'media'   => $this->media->id()->asString(),
            'medias'  => \json_encode([$this->media->id()->asString()], JSON_THROW_ON_ERROR),
        ];
        $submittedPage   = new Page();
        $form         = $this->factory->create(PageType::class, $submittedPage);

        $expectedPage = new Page();
        $expectedPage->setMediaId($this->media->id());
        $expectedPage->setGallery([$this->media->id()->asString()]);
        $expectedPage->setMedia($this->media);
        $medias = new ArrayCollection();
        $medias->add($this->media);
        $expectedPage->setMedias($medias);

        $form->submit($formData);
        // This check ensures there are no transformation failures
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expectedPage, $submittedPage);
    }

    public function testItFormViewWithValidVars(): void
    {

        $submittedPage   = new Page();
        $formView        = $this->factory->create(PageType::class, $submittedPage)->createView();

        /* MediaId */
        $this->assertSame('/admin', $formView->vars['form']['mediaId']->vars['api_prefix']);
        $this->assertFalse($formView->vars['form']['mediaId']->vars['multiple_selection']);
        $this->assertFalse($formView->vars['form']['mediaId']->vars['association']);

        /* Gallery Json Array */
        $this->assertSame('/admin', $formView->vars['form']['gallery']->vars['api_prefix']);
        $this->assertTrue($formView->vars['form']['gallery']->vars['multiple_selection']);
        $this->assertFalse($formView->vars['form']['gallery']->vars['association']);

        /* Media Entity ManyToOne */
        $this->assertSame('/admin', $formView->vars['form']['media']->vars['api_prefix']);
        $this->assertFalse($formView->vars['form']['media']->vars['multiple_selection']);
        $this->assertTrue($formView->vars['form']['media']->vars['association']);

        /* Medias Doctrine Collection */
        $this->assertSame('/admin', $formView->vars['form']['medias']->vars['api_prefix']);
        $this->assertTrue($formView->vars['form']['medias']->vars['multiple_selection']);
        $this->assertTrue($formView->vars['form']['medias']->vars['association']);
    }

}

<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Presentation;

use Ranky\MediaBundle\Infrastructure\DependencyInjection\MediaBundleExtension;
use Ranky\MediaBundle\Tests\BaseIntegrationTestCase;
use Symfony\Component\Translation\Formatter\MessageFormatter;
use Symfony\Component\Translation\Loader\PhpFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslationTest extends BaseIntegrationTestCase
{
    /**
     * @return array<int, array<int,mixed>>
     */
    public function dataProviderLocaleAndTranslation(): array
    {
        return [
            ['en', 'title', 'Image and file management', []],
            [
                'en',
                'modal_title',
                'File details image.jpg <small>id: 1</small>',
                [
                    '{file_name}' => 'image.jpg',
                    '{id}' => 1,
                ],
            ],
            ['es', 'title', 'Gestión de imágenes y archivos', []],
            [
                'es',
                'modal_title',
                'Detalles del archivo image.jpg <small>id: 1</small>',
                [
                    '{file_name}' => 'image.jpg',
                    '{id}' => 1,
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderLocaleAndTranslation
     * @param string $locale
     * @param string $id
     * @param string $expected
     * @param array<string> $parameters
     * @return void
     */
    public function testItShouldGetValidTranslationByLocaleOnSymfonyRequest(
        string $locale,
        string $id,
        string $expected,
        array $parameters = []
    ): void {
        $translator = $this->getService(TranslatorInterface::class);
        $this->assertSame(
            $expected,
            $translator->trans($id, $parameters, MediaBundleExtension::CONFIG_DOMAIN_NAME, $locale)
        );
    }

    /**
     * @dataProvider dataProviderLocaleAndTranslation
     * @param string $locale
     * @param string $id
     * @param string $expected
     * @param array<string> $parameters
     * @return void
     */
    public function testItShouldGetValidTranslationByLocale(
        string $locale,
        string $id,
        string $expected,
        array $parameters = []
    ): void {
        $translator = new Translator($locale, new MessageFormatter());
        $translator->setFallbackLocales([$locale]);
        $translator->addLoader('php', new PhpFileLoader());
        $translator->addResource(
            'php',
            $_SERVER['PWD'].'/translations/ranky_media+intl-icu.'.$locale.'.php',
            $locale,
            MediaBundleExtension::CONFIG_DOMAIN_NAME
        );

        $this->assertSame(
            $expected,
            $translator->trans($id, $parameters, MediaBundleExtension::CONFIG_DOMAIN_NAME)
        );
    }
}

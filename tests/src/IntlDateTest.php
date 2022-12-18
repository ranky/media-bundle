<?php

declare(strict_types=1);


namespace Ranky\MediaBundle\Tests;

use PHPUnit\Framework\TestCase;

class IntlDateTest extends TestCase
{
    public function testIntlDateFormatter(): void
    {
        \ini_set('intl.default_locale', 'es');
        $intlDate = \IntlDateFormatter::create(
            'es',
            \IntlDateFormatter::LONG,
            \IntlDateFormatter::SHORT,
            'Europe/Madrid',
            \IntlDateFormatter::GREGORIAN
        );
        $this->assertSame('es', \Locale::getDefault());
        $this->assertSame('es', $intlDate->getLocale());
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d H:i', '2022-12-13 23:59');
        $this->assertSame('13 de diciembre de 2022, 23:59', $date ? $intlDate->format($date) : null);
    }
}

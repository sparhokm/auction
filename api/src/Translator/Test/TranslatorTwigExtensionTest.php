<?php

declare(strict_types=1);

namespace App\Translator\Test;

use App\Translator\TranslatorTwigExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * @internal
 */
#[CoversClass(TranslatorTwigExtension::class)]
final class TranslatorTwigExtensionTest extends TestCase
{
    public function testActive(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->expects(self::once())->method('trans')->with('message', [], 'domain')->willReturn('result');

        $twig = new Environment(new ArrayLoader([
            'page.html.twig' => '<p>{{ trans(\'message\', [], \'domain\') }}</p>',
        ]));

        $twig->addExtension(new TranslatorTwigExtension($translator));

        self::assertSame('<p>result</p>', $twig->render('page.html.twig'));
    }
}

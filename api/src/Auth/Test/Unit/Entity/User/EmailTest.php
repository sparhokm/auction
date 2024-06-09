<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\Email;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Email::class)]
final class EmailTest extends TestCase
{
    public function testSuccess(): void
    {
        $email = new Email($value = 'email@app.test');
        self::assertSame($value, $email->getValue());
        self::assertTrue($email->isEqualTo(new Email($value)));
    }

    public function testCase(): void
    {
        $email = new Email('EmaiL@app.test');
        self::assertSame('email@app.test', $email->getValue());
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('not-email');
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('');
    }
}

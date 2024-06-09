<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\Token;

use App\Auth\Entity\User\Token;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @internal
 */
#[CoversClass(Token::class)]
#[CoversFunction('validate')]
final class ValidateTest extends TestCase
{
    public function testSuccess(): void
    {
        $token = new Token(
            $value = Uuid::uuid4()->toString(),
            $expires = new DateTimeImmutable()
        );

        $token->validate($value, $expires->modify('-1 secs'));
        self::assertTrue(true);
    }

    public function testWrong(): void
    {
        $token = new Token(
            Uuid::uuid4()->toString(),
            $expires = new DateTimeImmutable()
        );

        $this->expectExceptionMessage('Token is invalid.');
        $token->validate(Uuid::uuid4()->toString(), $expires->modify('-1 secs'));
    }

    public function testExpired(): void
    {
        $token = new Token(
            $value = Uuid::uuid4()->toString(),
            $expires = new DateTimeImmutable()
        );

        $this->expectExceptionMessage('Token is expired.');
        $token->validate($value, $expires->modify('+1 secs'));
    }
}

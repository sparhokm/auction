<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\Token;

use App\Auth\Entity\User\Token;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @internal
 */
final class ExpiresTest extends TestCase
{
    public function testNo(): void
    {
        $token = new Token(
            Uuid::uuid4()->toString(),
            $expires = new DateTimeImmutable()
        );

        self::assertFalse($token->isExpiredTo($expires->modify('-1 secs')));
        self::assertTrue($token->isExpiredTo($expires));
        self::assertTrue($token->isExpiredTo($expires->modify('+1 secs')));
    }
}

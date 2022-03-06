<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User;

use App\Auth\Test\Builder\UserBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Auth\Entity\User\User
 *
 * @internal
 */
final class RemoveTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->build();

        $user->remove();
        self::assertTrue(true);
    }

    public function testActive(): void
    {
        $user = (new UserBuilder())->active()->build();

        $this->expectExceptionMessage('Unable to remove active user.');
        $user->remove();
    }
}

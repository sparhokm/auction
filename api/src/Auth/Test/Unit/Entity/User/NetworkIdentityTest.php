<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\Network;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Auth\Entity\User\Network
 */
class NetworkIdentityTest extends TestCase
{
    public function testSuccess(): void
    {
        $network = new Network($name = 'google', $identity = 'google-1');

        self::assertEquals($name, $network->getName());
        self::assertEquals($identity, $network->getIdentity());
    }

    public function testEmptyName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Network('google', '');
    }

    public function testEmptyIdentity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Network('', 'google-1');
    }

    public function testEquals(): void
    {
        $network = new Network($name = 'google', $identity = 'google-1');

        self::assertTrue($network->isEqualTo(new Network($name, $identity)));
        self::assertFalse($network->isEqualTo(new Network($name, 'google-2')));
        self::assertFalse($network->isEqualTo(new Network('vk', $identity)));
    }
}

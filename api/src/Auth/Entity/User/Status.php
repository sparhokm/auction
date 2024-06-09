<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Override;
use Stringable;
use Webmozart\Assert\Assert;

final class Status implements Stringable
{
    public const string WAIT = 'wait';
    public const string ACTIVE = 'active';

    private readonly string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::WAIT,
            self::ACTIVE,
        ]);
        $this->name = $name;
    }

    #[Override]
    public function __toString(): string
    {
        return $this->getName();
    }

    public static function wait(): self
    {
        return new self(self::WAIT);
    }

    public static function active(): self
    {
        return new self(self::ACTIVE);
    }

    public function isWait(): bool
    {
        return $this->name === self::WAIT;
    }

    public function isActive(): bool
    {
        return $this->name === self::ACTIVE;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

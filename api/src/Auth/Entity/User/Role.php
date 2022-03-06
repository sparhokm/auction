<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Webmozart\Assert\Assert;

final class Role
{
    public const USER = 'user';
    public const ADMIN = 'admin';

    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [self::ADMIN, self::USER]);

        $this->name = $name;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public static function user(): self
    {
        return new self(self::USER);
    }

    public function getName(): string
    {
        return $this->name;
    }
}

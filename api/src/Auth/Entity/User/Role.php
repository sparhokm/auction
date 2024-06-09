<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Override;
use Stringable;
use Webmozart\Assert\Assert;

final class Role implements Stringable
{
    public const string USER = 'user';
    public const string ADMIN = 'admin';

    private readonly string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [self::ADMIN, self::USER]);

        $this->name = $name;
    }

    #[Override]
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

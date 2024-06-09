<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Override;
use Stringable;
use Webmozart\Assert\Assert;

final readonly class Email implements Stringable
{
    private string $value;

    public function __construct(string $value)
    {
        Assert::email($value);
        $this->value = mb_strtolower($value);
    }

    #[Override]
    public function __toString(): string
    {
        return $this->getValue();
    }

    public function isEqualTo(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}

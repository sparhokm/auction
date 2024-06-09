<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
final readonly class Token
{
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $value;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $expires;

    public function __construct(string $value, DateTimeImmutable $expires)
    {
        Assert::uuid($value);
        $this->value = mb_strtolower($value);
        $this->expires = $expires;
    }

    public function getValue(): string
    {
        return $this->value ?? throw new DomainException('Value is empty.');
    }

    public function getExpires(): DateTimeImmutable
    {
        return $this->expires ?? throw new DomainException('Expires is empty.');
    }

    public function validate(string $value, DateTimeImmutable $date): void
    {
        if (!$this->isEqualTo($value)) {
            throw new DomainException('Token is invalid.');
        }

        if ($this->isExpiredTo($date)) {
            throw new DomainException('Token is expired.');
        }
    }

    public function isExpiredTo(DateTimeImmutable $date): bool
    {
        return $this->expires <= $date;
    }

    /**
     * @internal
     */
    public function isEmpty(): bool
    {
        return $this->value === null;
    }

    private function isEqualTo(string $value): bool
    {
        return $this->value === $value;
    }
}

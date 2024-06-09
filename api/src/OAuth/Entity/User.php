<?php

declare(strict_types=1);

namespace App\OAuth\Entity;

use League\OAuth2\Server\Entities\UserEntityInterface;
use Override;
use Webmozart\Assert\Assert;

final class User implements UserEntityInterface
{
    private readonly string $identifier;

    public function __construct(string $identifier)
    {
        Assert::uuid($identifier);

        $this->identifier = $identifier;
    }

    #[Override]
    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}

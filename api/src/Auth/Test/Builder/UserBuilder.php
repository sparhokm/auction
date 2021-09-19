<?php

declare(strict_types=1);

namespace App\Auth\Test\Builder;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Status;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use Ramsey\Uuid\Uuid;

class UserBuilder
{
    private Id $id;
    private \DateTimeImmutable $date;
    private Email $email;
    private string $passwordHash;
    private Token $joinConfirmToken;
    public Status $status;
    private bool $active = false;

    public function __construct()
    {
        $this->id = Id::generate();
        $this->date = new \DateTimeImmutable();
        $this->email = new Email('mail@example.com');
        $this->passwordHash = 'hash';
        $this->joinConfirmToken = new Token(Uuid::uuid4()->toString(), $this->date->modify('+1 day'));
    }

    public function withJoinConfirmToken(Token $token): self
    {
        $clone = clone $this;
        $clone->joinConfirmToken = $token;
        return $clone;
    }

    public function active(): self
    {
        $clone = clone $this;
        $clone->active = true;
        return $clone;
    }

    public function build(): User
    {
        $user = new User(
            $this->id,
            $this->date,
            $this->email,
            $this->passwordHash,
            $this->joinConfirmToken
        );

        if ($this->active) {
            $user->confirmJoin(
                $this->joinConfirmToken->getValue(),
                $this->joinConfirmToken->getExpires()->modify('-1 day')
            );
        }

        return $user;
    }
}

<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use DomainException;

interface UserRepository
{
    public function hasByEmail(Email $email): bool;
    public function hasByNetwork(NetworkIdentity $network): ?User;
    public function findByConfirmToken(string $token): ?User;

    /**
     * @param Id $id
     * @return User
     * @throws DomainException
     */
    public function get(Id $id): User;
    public function add(User $user): void;
}

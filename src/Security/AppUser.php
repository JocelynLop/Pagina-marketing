<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class AppUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @param list<string> $roles
     */
    public function __construct(
        private readonly string $email,
        private readonly string $password,
        private readonly array $roles = ['ROLE_USER'],
    ) {
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_values(array_unique($roles));
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function eraseCredentials(): void
    {
    }
}

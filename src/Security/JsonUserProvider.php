<?php

namespace App\Security;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @implements UserProviderInterface<AppUser>
 */
final class JsonUserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    public function __construct(
        private readonly UserStore $userStore,
        #[Autowire(env: 'ADMIN_EMAIL')]
        private readonly string $adminEmail,
        #[Autowire(env: 'ADMIN_PASSWORD_HASH')]
        private readonly string $adminPasswordHash,
    ) {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $email = mb_strtolower(trim($identifier));

        if ($email === mb_strtolower(trim($this->adminEmail))) {
            return new AppUser($email, $this->adminPasswordHash, ['ROLE_ADMIN']);
        }

        $user = $this->userStore->find($email);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return AppUser::class === $class || is_subclass_of($class, AppUser::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof AppUser || in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return;
        }

        $this->userStore->create($user->getUserIdentifier(), $newHashedPassword, $user->getRoles());
    }
}

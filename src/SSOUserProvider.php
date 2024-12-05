<?php

namespace SPSOstrov\SSOBundle;

use Exception;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SSOUserProvider implements UserProviderInterface
{
    private SSORoleDeciderInterface $roleDecider;

    public function __construct(SSORoleDeciderInterface $roleDecider)
    {
        $this->roleDecider = $roleDecider;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        throw new Exception('User loading not implemented');
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof SSOUser) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        $roles = $this->roleDecider->decideRoles($user);
        $user->setRoles($roles);

        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return SSOUser::class === $class || is_subclass_of($class, SSOUser::class);
    }
}

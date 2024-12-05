<?php

namespace SPSOstrov\SSOBundle;

use Exception;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SSOUserProvider implements UserProviderInterface
{
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        throw new Exception('User loading not implemented');
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof SSOUser) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        return $user;
    }

    public function supports(string $class): bool
    {
        return $this->supportsClass($class);
    }

    public function supportsClass(string $class): bool
    {
        return SSOUser::class === $class || is_subclass_of($class, SSOUser::class);
    }
}

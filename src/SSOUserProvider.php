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
    private ?SSOUserDataProviderInterface $userDataProvider;

    public function __construct(SSORoleDeciderInterface $roleDecider, ?SSOUserDataProviderInterface $userDataProvider)
    {
        $this->roleDecider = $roleDecider;
        $this->userDataProvider = $userDataProvider;
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

        if ($this->userDataProvider !== null) {
            $userData = $this->userDataProvider->getUserData($user);
            $user->setUserData($userData);
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

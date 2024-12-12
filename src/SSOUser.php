<?php

namespace SPSOstrov\SSOBundle;

use Symfony\Component\Security\Core\User\UserInterface;
use SPSOstrov\SSO\SSOUser as SSOUserBase;

class SSOUser extends SSOUserBase implements UserInterface
{
    private array $roles = [];
    private $userData = null;

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setUserData($userData): self
    {
        $this->userData = $userData;
    }

    public function getUserData()
    {
        return $this->userData;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->getLogin();
    }
}

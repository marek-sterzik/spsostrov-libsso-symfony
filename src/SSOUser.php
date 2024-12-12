<?php

namespace SPSOstrov\SSOBundle;

use Symfony\Component\Security\Core\User\UserInterface;
use SPSOstrov\SSO\SSOUser as SSOUserBase;

class SSOUser extends SSOUserBase implements UserInterface
{
    private array $roles = [];
    private ?SSOUserPrivate $userData = null;

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles);
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setUserData($userData): self
    {
        if ($this->userData === null) {
            $this->userData = new SSOUserPrivate();
        }
        $this->userData->setData($userData);
        return $this;
    }

    public function getUserData()
    {
        return isset($this->userData) ? $this->userData->getData() : null;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->getLogin();
    }
}

<?php

namespace SPSOstrov\SSOBundle;

use Symfony\Component\Security\Core\User\UserInterface;
use SPSOstrov\SSO\SSOUser as SSOUserBase;

class SSOUser extends SSOUserBase implements UserInterface
{
    private array $roles = [];

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->getLogin();
    }
}

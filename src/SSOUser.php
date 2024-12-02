<?php

namespace SPSOstrov\SSOBundle;

use Symfony\Component\Security\Core\User\UserInterface;
use SPSOstrov\SSO\SSOUser as SSOUserBase;

class SSOUser extends SSOUserBase implements UserInterface
{
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->getLogin();
    }
}

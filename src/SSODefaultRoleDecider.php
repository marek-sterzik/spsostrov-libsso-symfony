<?php

namespace SPSOstrov\SSOBundle;

class SSODefaultRoleDecider implements SSORoleDeciderInterface
{
    public function decideRoles(SSOUser $user): array
    {
        $roles = [];
        if ($user->isTeacher()) {
            $roles[] = 'ROLE_TEACHER';
        }
        if ($user->isStudent()) {
            $roles[] = 'ROLE_STUDENT';
        }
        $roles[] = 'ROLE_USER';
        return $roles;
    }
}

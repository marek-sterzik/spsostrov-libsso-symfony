<?php

namespace SPSOstrov\SSOBundle;

class SSODefaultRoleDecider implements SSORoleDecider
{
    public function decideRoles(SSOUser $user): array
    {
        $roles = ['ROLE_USER'];
        if ($user->isStudent()) {
            $roles[] = 'ROLE_STUDENT';
        }
        if ($user->isTeacher()) {
            $roles[] = 'ROLE_TEACHER';
        }
    }
}

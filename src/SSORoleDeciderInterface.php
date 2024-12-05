<?php

namespace SPSOstrov\SSOBundle;

interface SSORoleDeciderInterface
{
    public function decideRoles(SSOUser $user): array;
}

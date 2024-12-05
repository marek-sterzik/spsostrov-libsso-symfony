<?php

namespace SPSOstrov\SSOBundle;

interface SSORoleDecider
{
    public function decideRoles(SSOUser $user): array;
}

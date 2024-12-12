<?php

namespace SPSOstrov\SSOBundle;

interface SSOUserDataProviderInterface
{
    public function getUserData(SSOUser $user);
}

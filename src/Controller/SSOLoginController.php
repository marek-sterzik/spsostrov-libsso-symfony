<?php

namespace SPSOstrov\SSOBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

use SPSOstrov\SSO\SSO;
use SPSOstrov\SSOBundle\SSOUser;

class SSOLoginController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/sso-login", name="sso_login")
     */
    public function index(): Response
    {
        $sso = new SSO(null, null, SSOUser::class);
        $user = $sso->doLogin();
        if ($user !== null) {
            $this->security->login($user);
        }
        return new Response("sso-login " . (($user === null) ? 'failed' : 'succeeded'));
    }
}

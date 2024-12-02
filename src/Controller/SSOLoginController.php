<?php

namespace SPSOstrov\SSOBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use SPSOstrov\SSO\SSO;

class SSOLoginController
{
    /**
     * @Route("/sso-login", name="sso_login")
     */
    public function index(): Response
    {
        $sso = new SSO();
        $user = $sso->doLogin();
        var_dump($user);
        return new Response("sso-login");
    }
}

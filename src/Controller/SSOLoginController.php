<?php

namespace SPSOstrov\SSOBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SSOLoginController
{
    /**
     * @Route("/sso-login", name="sso_login")
     */
    public function index(): Response
    {
        return new Response("sso-login");
    }
}

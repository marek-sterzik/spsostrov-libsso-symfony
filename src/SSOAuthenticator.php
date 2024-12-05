<?php

namespace SPSOstrov\SSOBundle;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Authenticator\InteractiveAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\HttpUtils;
use SPSOstrov\SSO\SSO;

class SSOAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface, InteractiveAuthenticatorInterface
{
    const DEFAULT_OPTIONS = [];

    private UserProviderInterface $userProvider;
    private HttpUtils $httpUtils;
    private array $options;
    private SSO $sso;

    public function __construct(
        UserProviderInterface $userProvider,
        HttpUtils $httpUtils,
        array $options
    ) {
        if (!($userProvider instanceof SSOUserProvider)) {
            throw new Exception("spsostrov_sso authenticator works only with spsostrov_sso user provider");
        }
        $this->userProvider = $userProvider;
        $this->httpUtils = $httpUtils;
        $this->options = array_merge(self::DEFAULT_OPTIONS, $options);
        $this->sso = new SSO(null, null, SSOUser::class);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->httpUtils->generateUri($request, $this->options['login_path']);
    }

    protected function getSelfUrl(Request $request): string
    {
        return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . $request->getPathInfo();
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $url = $this->getLoginUrl($request);
        return new RedirectResponse($url);
    }

    public function supports(Request $request): ?bool
    {
        return $this->getLoginUrl($request) === $this->getSelfUrl($request);
    }

    public function authenticate(Request $request): Passport
    {
        $token = $request->query->get('token');
        if (!is_string($token)) {
            throw new TokenNotFoundException('Missing token');
        }
        $user = $this->sso->getLoginCredentials($token, $this->getSelfUrl($request));
        if ($user === null) {
            throw new AccessDeniedException("bad token"); 
        }
        $passport = new SelfValidatingPassport(
            new UserBadge(
                $user->getLogin(),
                fn ($userIdentifier) => $user
            )
        );

        return $passport;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $url = $this->sso->getRedirectUrl($this->getSelfUrl($request));
        return new RedirectResponse($url);
    }

    public function isInteractive(): bool
    {
        return true;
    }
}

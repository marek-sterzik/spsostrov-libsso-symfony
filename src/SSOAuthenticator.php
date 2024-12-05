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
    const LOGIN_SESSION_KEY = self::class;

    private UserProviderInterface $userProvider;
    private HttpUtils $httpUtils;
    private array $options;
    private SSO $sso;

    public function __construct(
        UserProviderInterface $userProvider,
        HttpUtils $httpUtils,
        SSO $sso,
        array $options
    ) {
        if (!($userProvider instanceof SSOUserProvider)) {
            throw new Exception("spsostrov_sso authenticator works only with spsostrov_sso user provider");
        }
        $this->userProvider = $userProvider;
        $this->httpUtils = $httpUtils;
        $this->sso = $sso;
        $this->options = array_merge(self::DEFAULT_OPTIONS, $options);
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
        $url = $this->getLoginUrl($request) . $this->getRedirectQueryString($request);
        return new RedirectResponse($url);
    }

    protected function getRedirectQueryString(Request $request): string
    {
        $redirectParam = $this->options['redirect_param'];
        if (empty($redirectParam)) {
            return "";
        }
        return sprintf("?%s=%s", urlencode($redirectParam), urlencode($request->getRequestUri()));
    }

    public function supports(Request $request): ?bool
    {
        return $this->getLoginUrl($request) === $this->getSelfUrl($request) && $request->query->get('ticket') !== '';
    }

    public function authenticate(Request $request): Passport
    {
        $token = $request->query->get('ticket');
        if (!is_string($token)) {
            $this->storeGetParametersToSession($request);
            throw new TokenNotFoundException('Missing token');
        }
        $user = $this->sso->getLoginCredentials($token, $this->getSelfUrl($request));
        if ($user === null) {
            throw new AccessDeniedException("bad token"); 
        }
        $passport = new SelfValidatingPassport(
            new UserBadge(
                $user->getLogin(),
                fn ($userIdentifier) => $this->userProvider->refreshUser($user)
            )
        );

        return $passport;
    }

    private function storeGetParametersToSession(Request $request): void
    {
        $query = $request->query->all();
        unset($query['ticket']);
        $request->getSession()->set(self::LOGIN_SESSION_KEY, $query);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $session = $request->getSession();
        $query = array_merge(
            $request->query->all(),
            $session->get(self::LOGIN_SESSION_KEY) ?? [],
            ["ticket" => ""]
        );
        $session->remove(self::LOGIN_SESSION_KEY);
        $queryString = http_build_query($query);
        if ($queryString !== '') {
            $queryString = '?' . $queryString;
        }
        return new RedirectResponse($this->getSelfUrl($request) . $queryString);
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

<?php

namespace SPSOstrov\SSOBundle;

use Symfony\Component\Security\Core\Exception\LogicException;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AuthenticatorFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Security\Http\HttpUtils;
use SPSOstrov\SSO\SSO;

class SSOAuthenticatorFactory implements AuthenticatorFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return -10;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey(): string
    {
        return 'spsostrov_sso';
    }

    public function addConfiguration(NodeDefinition $node): void
    {
        $node
            ->children()
                ->scalarNode('login_path')
                ->end()
                ->scalarNode('redirect_param')
                    ->defaultValue("back")
                ->end()
                ->scalarNode('provider')
                    ->defaultNull()
                ->end()
                ->scalarNode('sso_variant')
                    ->defaultValue('production')
                ->end()
                ->scalarNode('sso_gateway_url')
                    ->defaultNull()
                ->end()
                ->scalarNode('sso_gateway_check_url')
                    ->defaultNull()
                ->end()
                ->scalarNode('sso_user_class')
                    ->defaultNull()
                ->end()
                ->scalarNode('sso')
                    ->defaultNull()
                ->end()
            ->end()
        ;
    }

    public function createAuthenticator(ContainerBuilder $container, string $firewallName, array $config, string $userProviderId): string
    {
        $authenticatorId = 'security.authenticator.spsostrov_sso.auth.' . $firewallName;

        $userProviderId = empty($config['provider']) ? $userProviderId : 'security.user.provider.concrete.' . $config['provider'];
        unset($config['provider']);

        if (isset($config['sso'])) {
            $ssoId = $config['sso'];
        } else {
            $urls = $this->getSsoGatewayUrls($config);
            $ssoId = 'security.authenticator.spsostrov_sso.sso.' . $firewallName;
            $container->register($ssoId, SSO::class)
                ->setArgument(0, $urls['sso_gateway_url'])
                ->setArgument(1, $urls['sso_gateway_check_url'])
                ->setArgument(2, $config['sso_user_class'] ?? SSOUser::class)
            ;
        }
        $container->register($authenticatorId, SSOAuthenticator::class)
            ->setArgument(0, new Reference($userProviderId))
            ->setArgument(1, new Reference(HttpUtils::class))
            ->setArgument(2, new Reference($ssoId))
            ->setArgument(3, $config)
        ;

        return $authenticatorId;
    }

    private function getSsoGatewayUrls($config): array
    {
        $urls = $this->getSsoGatewayUrlsByVariant($config['sso_variant']);
        if (isset($config['sso_gateway_url'])) {
            $urls['sso_gateway_url'] = $config['sso_gateway_url'];
        }
        if (isset($config['sso_gateway_check_url'])) {
            $urls['sso_gateway_check_url'] = $config['sso_gateway_check_url'];
        }
        return $urls;
    }

    private function getSsoGatewayUrlsByVariant(string $variant): array
    {
        if ($variant !== 'production') {
            throw new LogicException("Only production sso_variant is available by default");
        }
        return ["sso_gateway_url" => null, "sso_gateway_check_url" => null];
    }
}

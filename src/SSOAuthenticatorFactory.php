<?php

namespace SPSOstrov\SSOBundle;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AuthenticatorFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Security\Http\HttpUtils;

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
                ->scalarNode('sso')
                    ->defaultNull()
                ->end()
            ->end()
        ;
    }

    public function createAuthenticator(ContainerBuilder $container, string $firewallName, array $config, string $userProviderId): string
    {
        $authenticatorId = 'security.authenticator.spsostrov_sso.' . $firewallName;

        $userProviderId = empty($config['provider']) ? $userProviderId : 'security.user.provider.concrete.' . $config['provider'];
        unset($config['provider']);

        $ssoId = $config['sso'] ?? 'spsostrov.sso';

        $container->register($authenticatorId, SSOAuthenticator::class)
            ->setArgument(0, new Reference($userProviderId))
            ->setArgument(1, new Reference(HttpUtils::class))
            ->setArgument(2, new Reference($ssoId))
            ->setArgument(3, $config)
        ;

        return $authenticatorId;
    }
}

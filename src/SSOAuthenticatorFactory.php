<?php

namespace SPSOstrov\SSOBundle;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AuthenticatorFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

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
                ->scalarNode('provider')
                    ->defaultNull()
                ->end()
                ->scalarNode('login_path')
                ->end()
            ->end()
        ;
    }

    public function createAuthenticator(ContainerBuilder $container, string $firewallName, array $config, string $userProviderId): string
    {
        $authenticatorId = 'security.authenticator.spsostrov_sso.' . $firewallName;

        $userProviderId = empty($config['provider']) ? $userProviderId : 'security.user.provider.concrete.' . $config['provider'];

        $container->register($authenticatorId, SSOUserProvider::class)
            ->replaceArgument(0, new Reference($userProviderId))
            ->replaceArgument(2, $config)
        ;

        return $authenticatorId;
    }
}

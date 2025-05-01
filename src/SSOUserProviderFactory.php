<?php

namespace SPSOstrov\SSOBundle;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\UserProvider\UserProviderFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class SSOUserProviderFactory implements UserProviderFactoryInterface
{
    public function create(ContainerBuilder $container, string $id, array $config): void
    {
        $roleDeciderId = $config['role_decider'] ?? 'spsostrov.sso_role_decider';
        $userDataProviderId = $config['user_data_provider'] ?? null;

        $roleDecider = new Reference($roleDeciderId);
        $userDataProvider = isset($userDataProviderId) ? (new Reference($userDataProviderId)) : null;

        if ($roleDeciderId === 'spsostrov.sso_role_decider' &&
            !$container->hasDefinition('spsostrov.sso_role_decider')
        ) {
            $container->register("spsostrov.sso_role_decider", SSODefaultRoleDecider::class);
        }
        $container->register($id, SSOUserProvider::class)
            ->setArgument(0, $roleDecider)
            ->setArgument(1, $userDataProvider)
        ;
    }

    public function getKey(): string
    {
        return 'spsostrov_sso';
    }

    public function addConfiguration(NodeDefinition $node): void
    {
        $node
            ->children()
                ->scalarNode('role_decider')
                    ->defaultNull()
                ->end()
                ->scalarNode('user_data_provider')
                    ->defaultNull()
                ->end()
            ->end()
        ;
    }
}

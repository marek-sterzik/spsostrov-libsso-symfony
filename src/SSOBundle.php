<?php

namespace SPSOstrov\SSOBundle;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class SSOBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $extension = $container->getExtension('security');

        $extension->addUserProviderFactory(new SSOUserProviderFactory());
        $extension->addAuthenticatorFactory(new SSOAuthenticatorFactory());
    }
}

<?php

namespace SPSOstrov\SSOBundle;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class SSOBundle extends AbstractBundle
{
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');
        
        $extension = $container->getExtension('security');

        $extension->addUserProviderFactory(new SSOUserProviderFactory());

        //$extension->addAuthenticatorFactory(new JWTAuthenticatorFactory());

        /*
        $container->parameters()
            ->set('acme_hello.phrase', $config['phrase'])
        ;

        if ($config['scream']) {
            $container->services()
                ->get('acme_hello.printer')
                    ->class(ScreamingPrinter::class)
            ;
        }
        */
    }
}

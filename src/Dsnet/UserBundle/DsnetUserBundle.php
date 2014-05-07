<?php

namespace Dsnet\UserBundle;

use Dsnet\UserBundle\DependencyInjection\Security\Factory\OAuthFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DsnetUserBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension("security");
        $extension->addSecurityListenerFactory(new OAuthFactory());
    }
}

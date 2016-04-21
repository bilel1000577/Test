<?php

namespace Quizmoo\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Quizmoo\UserBundle\firewall\token\ApiAuthFactory;
class QuizmooUserBundle extends Bundle
{
	public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new ApiAuthFactory());
    }
   public function getParent()
  {
    return 'FOSUserBundle';
  }
}

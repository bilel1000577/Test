<?php
namespace Quizmoo\UserBundle\firewall\token;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;

class ApiAuthFactory implements SecurityFactoryInterface
{
	public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'security.authentication.provider.apiauth.'.$id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('apiauth.security.authentication.provider'));

        $listenerId = 'security.authentication.listener.apiauth.'.$id;
        $listener = $container->setDefinition($listenerId, new DefinitionDecorator('apiauth.security.authentication.listener'));

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'Api_Auth';
    }

    public function addConfiguration(NodeDefinition $node)
    {
    }
}
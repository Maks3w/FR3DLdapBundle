<?php

namespace FR3D\LdapBundle\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class HttpBasicLdapFactory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config, $userProviderId, $defaultEntryPointId)
    {
        // authentication provider
        $authProviderId = $this->createAuthProvider($container, $id, $userProviderId);

        // entry point
        $entryPointId = $this->createEntryPoint($container, $id, $config, $defaultEntryPointId);

        // authentication listener
        $listenerId = $this->createListener($container, $id, $entryPointId);

        return [$authProviderId, $listenerId, $entryPointId];
    }

    public function getPosition()
    {
        return 'http';
    }

    public function getKey()
    {
        return 'fr3d_ldap_httpbasic';
    }

    public function addConfiguration(NodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('provider')->end()
                ->scalarNode('realm')->defaultValue('Secured Area')->end()
            ->end()
        ;
    }

    protected function createAuthProvider(ContainerBuilder $container, $id, $userProviderId)
    {
        $provider = 'fr3d_ldap.security.authentication.provider';
        $providerId = $provider . '.' . $id;

        $container
            ->setDefinition($providerId, new DefinitionDecorator($provider))
            ->replaceArgument(1, $id) // Provider Key
            ->replaceArgument(2, new Reference($userProviderId)) // User Provider
        ;

        return $providerId;
    }

    protected function createListener(ContainerBuilder $container, $id, $entryPointId)
    {
        // listener
        $listenerId = 'security.authentication.listener.basic.' . $id;
        $listener = $container->setDefinition($listenerId, new DefinitionDecorator('security.authentication.listener.basic'));
        $listener->replaceArgument(2, $id);
        $listener->replaceArgument(3, new Reference($entryPointId));

        return $listenerId;
    }

    protected function createEntryPoint(ContainerBuilder $container, $id, $config, $defaultEntryPoint)
    {
        if (null !== $defaultEntryPoint) {
            return $defaultEntryPoint;
        }

        $entryPointId = 'security.authentication.basic_entry_point.' . $id;
        $container
            ->setDefinition($entryPointId, new DefinitionDecorator('security.authentication.basic_entry_point'))
            ->addArgument($config['realm'])
        ;

        return $entryPointId;
    }
}

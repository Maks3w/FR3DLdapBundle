<?php

namespace FR3D\LdapBundle\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class LdapFactory extends AbstractFactory
{

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'fr3d_ldap';
    }

    protected function getListenerId()
    {
        return 'security.authentication.listener.form';
    }

    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $provider = 'fr3d_ldap.security.authentication.provider.' . $id;

        $container
                ->setDefinition($provider, new DefinitionDecorator('fr3d_ldap.security.authentication.provider'))
                ->replaceArgument(1, $id) // Provider Key
                ->replaceArgument(2, new Reference($userProviderId)) // User Provider
        ;

        return $provider;
    }
}

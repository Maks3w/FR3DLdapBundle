<?php

namespace FR3D\LdapBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class FR3DLdapExtension extends Extension
{

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        foreach (array('services', 'validator') as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }

        $container->setAlias('fr3d_ldap.user_manager', $config['service']['user_manager']);
        $container->setAlias('fr3d_ldap.ldap_manager', $config['service']['ldap_manager']);

        $container->setParameter('fr3d_ldap.client.parameters', $config['client']);
        $container->setParameter('fr3d_ldap.ldap_manager.parameters', $config['user']);
    }

    public function getNamespace()
    {
        return 'fr3d_ldap';
    }
}

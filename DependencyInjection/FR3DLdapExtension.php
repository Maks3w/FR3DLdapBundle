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
        foreach (array('services', 'security', 'validator', 'ldap_driver') as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }

        $container->setAlias('fr3d_ldap.user_manager', $config['service']['user_manager']);
        $container->setAlias('fr3d_ldap.ldap_manager', $config['service']['ldap_manager']);
        $container->setAlias('fr3d_ldap.ldap_driver', $config['service']['ldap_driver']);

        foreach ($config['domains'] as &$domain)
        {
            if (!isset($domain['driver']['baseDn'])) {
                $domain['driver']['baseDn'] = $domain['user']['baseDn'];
            }
            if (!isset($domain['driver']['accountFilterFormat'])) {
                $domain['driver']['accountFilterFormat'] = $domain['user']['filter'];
            }
        }

        $container->setParameter('fr3d_ldap.domains.parameters', $config['domains']);
    }

    public function getNamespace()
    {
        return 'fr3d_ldap';
    }
}

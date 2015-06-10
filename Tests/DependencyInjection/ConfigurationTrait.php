<?php

namespace FR3D\LdapBundle\Tests\DependencyInjection;

/**
 * Provide default configuration options for the bundle and each section.
 */
trait ConfigurationTrait
{
    /**
     * Returns default configuration bundle configuration.
     *
     * @return array
     */
    protected function getDefaultConfig()
    {
        return [
            'driver' => $this->getDefaultDriverConfig(),
            'user' => $this->getDefaultUserConfig(),
            'service' => $this->getDefaultServiceConfig(),
        ];
    }

    /**
     * Returns default configuration for Driver subtree.
     *
     * Same as service parameter `fr3d_ldap.ldap_driver.parameters`
     *
     * @return array
     */
    protected function getDefaultDriverConfig()
    {
        return [
            'host' => 'ldap.hostname.local',
            'port' => 389,
            'useSsl' => false,
            'useStartTls' => false,
            'baseDn' => 'ou=Persons,dc=example,dc=com',
            'accountFilterFormat' => '',
            'bindRequiresDn' => false,
        ];
    }

    /**
     * Returns default configuration for User subtree.
     *
     * Same as service parameter `fr3d_ldap.ldap_manager.parameters`
     *
     * @return array
     */
    protected function getDefaultUserConfig()
    {
        return [
            'baseDn' => 'ou=Persons,dc=example,dc=com',
            'filter' => '',
            'usernameAttribute' => 'uid',
            'attributes' => [
                [
                    'ldap_attr' => 'uid',
                    'user_method' => 'setUsername',
                ],
            ],
        ];
    }

    /**
     * Returns default configuration for Service subtree.
     *
     * @return array
     */
    protected function getDefaultServiceConfig()
    {
        return [
            'user_hydrator' => 'fr3d_ldap.user_hydrator.default',
            'ldap_manager' => 'fr3d_ldap.ldap_manager.default',
            'ldap_driver' => 'fr3d_ldap.ldap_driver.zend',
        ];
    }
}

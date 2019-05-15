<?php

namespace FR3D\LdapBundle\Tests\Hydrator;

use FR3D\LdapBundle\Tests\DependencyInjection\ConfigurationTrait;

abstract class AbstractHydratorTestCase extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTrait {
        getDefaultUserConfig as parentGetDefaultUserConfig;
    }
    use HydratorInterfaceTestTrait;

    /**
     * Returns default configuration for User subtree.
     *
     * Same as service parameter `fr3d_ldap.ldap_manager.parameters`
     */
    protected function getDefaultUserConfig(): array
    {
        $config = $this->parentGetDefaultUserConfig();
        $config['attributes'][] = [
            'ldap_attr' => 'roles',
            'user_method' => 'setRoles',
        ];

        return $config;
    }
}

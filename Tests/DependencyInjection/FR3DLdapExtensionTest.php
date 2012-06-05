<?php

namespace FR3D\LdapBundle\Tests\DependencyInjection;

use FR3D\LdapBundle\DependencyInjection\FR3DLdapExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FR3DLdapExtensionTest extends \PHPUnit_Framework_TestCase
{

    /** @var ContainerBuilder */
    public $container;

    public function testConfigurationNamespace()
    {
        $container = new ContainerBuilder();
        $container->registerExtension(new FR3DLdapExtension());
        $this->assertTrue($container->hasExtension('fr3d_ldap'));
    }

    public function testLoadMinimalConfiguration()
    {
        $minRequiredConfig = array(
            'client' => array(
                'host' => 'ldap.hostname.local',
            ),
            'user' => array(
                'baseDn' => 'ou=Groups,dc=example,dc=com',
            ),
        );

        $defaultConfig = $this->getDefaultConfig();

        $container = new ContainerBuilder();
        $extension = new FR3DLdapExtension();

        $extension->load(array($minRequiredConfig), $container);

        $this->assertTrue($container->hasDefinition('fr3d_ldap.client'));
        $this->assertTrue($container->hasDefinition('fr3d_ldap.ldap_manager.default'));

        $this->assertEquals($defaultConfig['client'], $container->getParameter('fr3d_ldap.client.parameters'));
        $this->assertEquals($defaultConfig['user'], $container->getParameter('fr3d_ldap.ldap_manager.parameters'));

        $this->assertEquals('fos_user.user_manager', (string) $container->getAlias('fr3d_ldap.user_manager', 'user_manager alias failed'));
        $this->assertEquals('fr3d_ldap.ldap_manager.default', (string) $container->getAlias('fr3d_ldap.ldap_manager', 'ldap_manager alias failed'));
    }

    public function testLoadFullConfiguration()
    {
        $config                           = $this->getDefaultConfig();
        $config['client']['username']     = null;
        $config['client']['password']     = null;
        $config['client']['optReferrals'] = false;

        $container = new ContainerBuilder();
        $extension = new FR3DLdapExtension();

        $extension->load(array($config), $container);

        $this->assertEquals($config['client'], $container->getParameter('fr3d_ldap.client.parameters'));
        $this->assertEquals($config['user'], $container->getParameter('fr3d_ldap.ldap_manager.parameters'));
    }

    public function testSslConfiguration()
    {
        $config                          = $this->getDefaultConfig();
        $config['client']['useSsl']      = true;
        $config['client']['useStartTls'] = false;

        $container = new ContainerBuilder();
        $extension = new FR3DLdapExtension();

        $extension->load(array($config), $container);

        $this->assertEquals($config['client'], $container->getParameter('fr3d_ldap.client.parameters'));
    }

    public function testTlsConfiguration()
    {
        $config                          = $this->getDefaultConfig();
        $config['client']['useSsl']      = false;
        $config['client']['useStartTls'] = true;

        $container = new ContainerBuilder();
        $extension = new FR3DLdapExtension();

        $extension->load(array($config), $container);

        $this->assertEquals($config['client'], $container->getParameter('fr3d_ldap.client.parameters'));
    }

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testSslTlsExclusiveConfiguration()
    {
        $config                          = $this->getDefaultConfig();
        $config['client']['useSsl']      = true;
        $config['client']['useStartTls'] = true;

        $container = new ContainerBuilder();
        $extension = new FR3DLdapExtension();

        $extension->load(array($config), $container);
    }

    private function getDefaultConfig()
    {
        return array(
            'client' => array(
                'host'        => 'ldap.hostname.local',
                'port'        => 389,
                'version'     => 3,
                'useSsl'      => false,
                'useStartTls' => false,
            ),
            'user'        => array(
                'baseDn'     => 'ou=Groups,dc=example,dc=com',
                'filter'     => '',
                'attributes' => array(
                    array(
                        'ldap_attr'   => 'uid',
                        'user_method' => 'setUsername',
                    ),
                ),
            ),
            'service'     => array(
                'user_manager' => 'fos_user.user_manager',
                'ldap_manager' => 'fr3d_ldap.ldap_manager.default',
            ),
        );
    }
}

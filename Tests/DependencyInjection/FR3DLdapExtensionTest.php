<?php

namespace FR3D\LdapBundle\Tests\DependencyInjection;

use FR3D\LdapBundle\DependencyInjection\FR3DLdapExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FR3DLdapExtensionTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTrait;

    /** @var ContainerBuilder */
    public $container;

    public function testConfigurationNamespace()
    {
        $this->container = new ContainerBuilder();
        $this->container->registerExtension(new FR3DLdapExtension());
        self::assertTrue($this->container->hasExtension('fr3d_ldap'));
    }

    public function testLoadMinimalConfiguration()
    {
        $minRequiredConfig = [
            'driver' => [
                'host' => 'ldap.hostname.local',
            ],
            'user' => [
                'baseDn' => 'ou=Persons,dc=example,dc=com',
            ],
        ];

        $defaultConfig = $this->getDefaultConfig();

        $this->container = new ContainerBuilder();
        $extension = new FR3DLdapExtension();

        $extension->load([$minRequiredConfig], $this->container);

        self::assertHasDefinition('fr3d_ldap.ldap_driver');
        self::assertHasDefinition('fr3d_ldap.ldap_manager.default');

        self::assertParameter($defaultConfig['driver'], 'fr3d_ldap.ldap_driver.parameters');
        self::assertParameter($defaultConfig['user'], 'fr3d_ldap.ldap_manager.parameters');

        self::assertAlias('fr3d_ldap.user_hydrator.default', 'fr3d_ldap.user_hydrator');
        self::assertAlias('fr3d_ldap.ldap_manager.default', 'fr3d_ldap.ldap_manager');
        self::assertAlias('fr3d_ldap.ldap_driver.zend', 'fr3d_ldap.ldap_driver');
    }

    public function testLoadFullConfiguration()
    {
        $config = $this->getDefaultConfig();
        $config['driver']['username'] = null;
        $config['driver']['password'] = null;
        $config['driver']['optReferrals'] = false;

        $this->container = new ContainerBuilder();
        $extension = new FR3DLdapExtension();

        $extension->load([$config], $this->container);

        self::assertEquals($config['driver'], $this->container->getParameter('fr3d_ldap.ldap_driver.parameters'));
        self::assertEquals($config['user'], $this->container->getParameter('fr3d_ldap.ldap_manager.parameters'));
    }

    public function testLoadDriverConfiguration()
    {
        $config = $this->getDefaultConfig();
        $config['driver']['accountFilterFormat'] = '(%(uid=%s))';

        $this->container = new ContainerBuilder();
        $extension = new FR3DLdapExtension();

        $extension->load([$config], $this->container);

        self::assertEquals($config['driver'], $this->container->getParameter('fr3d_ldap.ldap_driver.parameters'));
        self::assertEquals($config['user'], $this->container->getParameter('fr3d_ldap.ldap_manager.parameters'));
    }

    public function testSslConfiguration()
    {
        $config = $this->getDefaultConfig();
        $config['driver']['useSsl'] = true;
        $config['driver']['useStartTls'] = false;

        $this->container = new ContainerBuilder();
        $extension = new FR3DLdapExtension();

        $extension->load([$config], $this->container);

        self::assertEquals($config['driver'], $this->container->getParameter('fr3d_ldap.ldap_driver.parameters'));
    }

    public function testTlsConfiguration()
    {
        $config = $this->getDefaultConfig();
        $config['driver']['useSsl'] = false;
        $config['driver']['useStartTls'] = true;

        $this->container = new ContainerBuilder();
        $extension = new FR3DLdapExtension();

        $extension->load([$config], $this->container);

        self::assertEquals($config['driver'], $this->container->getParameter('fr3d_ldap.ldap_driver.parameters'));
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testSslTlsExclusiveConfiguration()
    {
        $config = $this->getDefaultConfig();
        $config['driver']['useSsl'] = true;
        $config['driver']['useStartTls'] = true;

        $this->container = new ContainerBuilder();
        $extension = new FR3DLdapExtension();

        $extension->load([$config], $this->container);
    }

    private function assertAlias($value, $key)
    {
        self::assertEquals($value, (string) $this->container->getAlias($key), sprintf('%s alias is not correct', $key));
    }

    private function assertParameter($value, $key)
    {
        self::assertEquals($value, $this->container->getParameter($key), sprintf('%s parameter is not correct', $key));
    }

    private function assertHasDefinition($id)
    {
        self::assertTrue(($this->container->hasDefinition($id) ?: $this->container->hasAlias($id)), sprintf('%s definition is not set', $id));
    }

    protected function tearDown()
    {
        unset($this->container);
    }
}

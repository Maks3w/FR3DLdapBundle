<?php

namespace FR3D\LdapBundle\Tests\Driver;

require_once 'LDAPVirtual/zend-ldap_php-ldap_override.php';

abstract class AbstractLdapDriverTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!function_exists('ldap_connect')) {
            $this->markTestSkipped('PHP LDAP extension not loaded');
        }

        global $ldapServer;

        $ldapServer = $this->getMock('FR3D\LdapBundle\Tests\Driver\LDAPVirtual\LDAPVirtualInterface');

        $ldapServer->expects($this->any())
                ->method('ldap_connect')
                ->will($this->returnValue(true)
        );

        $ldapServer->expects($this->any())
                ->method('ldap_start_tls')
                ->will($this->returnValue(true)
        );
    }

    protected function tearDown()
    {
        global $ldapServer;

        $ldapServer = null;
    }

    protected function getOptions()
    {
        $options = [
            'host' => 'ldap.example.com',
            'port' => 389,
            'useStartTls' => true,
        ];

        return $options;
    }
}

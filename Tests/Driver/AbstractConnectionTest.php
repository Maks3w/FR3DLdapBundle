<?php

namespace FR3D\LdapBundle\Tests\Driver;

require_once 'LDAPVirtual/LDAPVirtualInterface.php';
require_once 'LDAPVirtual/zend-ldap_php-ldap_override.php';
require_once 'LDAPVirtual/fr3d-ldapbundle-driver_php-ldap_override.php';

abstract class AbstractConnectionTest extends \PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        if (getenv('TRAVIS')) {
            $this->markTestSkipped("LDAPVirtual tests are not enabled");
        }

        global $ldapServer;

        $ldapServer = $this->getMock('\LDAPVirtualInterface');

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
        $options = array(
            'host'        => 'ldap.example.com',
            'port'        => 389,
            'useStartTls' => true,
        );

        return $options;
    }
}

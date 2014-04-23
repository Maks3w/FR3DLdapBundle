<?php

namespace FR3D\LdapBundle\Tests\Driver;

use FR3D\LdapBundle\Driver\LegacyLdapDriver;
use FR3D\LdapBundle\Tests\TestUser;

class LegacyLdapDriverTest extends AbstractLdapDriverTest
{
    /**
     * @var LegacyLdapDriver
     */
    protected $legacyLdapDriver;

    protected function setUp()
    {
        $this->legacyLdapDriver = new LegacyLdapDriver($this->getOptions());

        parent::setUp();
    }

    public function testSearch()
    {
        global $ldapServer;

        $baseDn     = 'ou=example,dc=org';
        $filter     = '(&(uid=test_username))';
        $attributes = array('uid');

        $entry = array(
            'dn'  => 'uid=test_username,ou=example,dc=org',
            'uid' => array('test_username'),
        );
        $expect = array(
            'count' => 1,
            $entry,
        );

        $search_result = 2;

        $ldapServer->expects($this->once())
                ->method('ldap_search')
                ->with($this->equalTo($baseDn), $this->equalTo($filter), $this->equalTo($attributes))
                ->will($this->returnValue($search_result));

        $ldapServer->expects($this->once())
                ->method('ldap_get_entries')
                ->with($this->equalTo($search_result))
                ->will($this->returnValue($expect));

        $this->assertEquals($expect, $this->legacyLdapDriver->search($baseDn, $filter, $attributes));
    }

    // Bind (bindRequireDn=false)
    /**
     * @dataProvider provideTestBind
     */
    public function testBind($bind_rdn, $password, $expect)
    {
        global $ldapServer;

        $user = new TestUser();
        $user->setUsername($bind_rdn);

        $ldapServer->expects($this->once())
                ->method('ldap_bind')
                ->will($this->returnValueMap($this->provideTestBind()));

        $this->assertEquals($expect, $this->legacyLdapDriver->bind($user, $password));
    }

    public function provideTestBind()
    {
        return array(
            // Username
            array('test_username', 'password', true),
            array('bad_username', 'password', false),
            array('test_username', 'bad_password', false),
            // DN
            array('uid=test_username,ou=example,dc=com', 'password', true),
            array('uid=bad_username,ou=example,dc=com', 'password', false),
            array('uid=test_username,ou=example,dc=com', 'bad_password', false),
        );
    }

    public function testBindUserInterfaceByUsername()
    {
        global $ldapServer;

        $username = 'test_username';
        $password = 'password';
        $user     = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');

        $user->expects($this->once())
                ->method('getUsername')
                ->will($this->returnValue($username));

        $ldapServer->expects($this->once())
                ->method('ldap_bind')
                ->with($this->equalTo($username), $this->equalTo($password))
                ->will($this->returnValue(true));

        $this->assertTrue($this->legacyLdapDriver->bind($user, $password));
    }

    // Bind DN (bindRequireDn=true)
    public function testBindByDnFromUsername()
    {
        global $ldapServer;

        $options                        = $this->getOptions();
        $options['baseDn']              = 'ou=example,dc=org';
        $options['accountFilterFormat'] = '(&(uid=%s))';
        $options['bindRequiresDn']      = true;
        $this->legacyLdapDriver = new LegacyLdapDriver($options);

        $baseDn = 'ou=example,dc=org';
        $filter = '(&(uid=test_username))';

        $username = 'test_username';
        $password = 'password';
        $user     = new TestUser();
        $user->setUsername($username);

        $entry = array(
            'dn'  => 'uid=test_username,ou=example,dc=org',
            'uid' => array('test_username'),
        );
        $result = array(
            'count' => 1,
            $entry,
        );

        $ldapServer->expects($this->once())
                ->method('ldap_search')
                ->with($this->equalTo($baseDn), $this->equalTo($filter))
                ->will($this->returnValue(2));

        $ldapServer->expects($this->once())
                ->method('ldap_get_entries')
                ->with($this->equalTo(2))
                ->will($this->returnValue($result));

        $ldapServer->expects($this->once())
                ->method('ldap_bind')
                ->with($this->equalTo($entry['dn']), $this->equalTo($password))
                ->will($this->returnValue(true));

        $this->assertTrue($this->legacyLdapDriver->bind($user, $password));
    }

    public function testBindByDnFromBadUsername()
    {
        global $ldapServer;

        $options                        = $this->getOptions();
        $options['baseDn']              = 'ou=example,dc=org';
        $options['accountFilterFormat'] = '(&(uid=%s))';
        $options['bindRequiresDn']      = true;
        $this->legacyLdapDriver = new LegacyLdapDriver($options);

        $username = 'bad_username';
        $password = 'password';
        $user     = new TestUser();
        $user->setUsername($username);

        $result = array(
            'count' => 0,
        );

        $ldapServer->expects($this->once())
                ->method('ldap_search')
                ->will($this->returnValue(2));

        $ldapServer->expects($this->once())
                ->method('ldap_get_entries')
                ->will($this->returnValue($result));

        $this->assertFalse($this->legacyLdapDriver->bind($user, $password));
    }
}

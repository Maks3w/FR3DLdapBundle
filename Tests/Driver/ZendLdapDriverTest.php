<?php

namespace FR3D\LdapBundle\Tests\Driver;

use FR3D\LdapBundle\Driver\ZendLdapDriver;
use FR3D\LdapBundle\Tests\TestUser;
use Zend\Ldap\Ldap;

/**
 * Test class for ZendLdapDriver.
 */
class ZendLdapDriverTest extends AbstractLdapDriverTest
{
    /**
     * @var \Zend\Ldap\Ldap
     */
    protected $zend;

    /**
     * @var ZendLdapDriver
     */
    protected $zendLdapDriver;

    /**
     * Sets up the fixture, for example, opens a network Driver.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->zend = new Ldap($this->getOptions());
        $this->zendLdapDriver = new ZendLdapDriver($this->zend);
    }

    public function testSearch()
    {
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

        $this->zend = $this->getMockBuilder('Zend\Ldap\Ldap')
                ->getMock();
        $this->zendLdapDriver = new ZendLdapDriver($this->zend);
        $this->zend->expects($this->once())
                ->method('searchEntries')
                ->with($this->equalTo($filter), $this->equalTo($baseDn), $this->equalTo(Ldap::SEARCH_SCOPE_SUB), $this->equalTo($attributes))
                ->will($this->returnValue(array($entry)));

        $this->assertEquals($expect, $this->zendLdapDriver->search($baseDn, $filter, $attributes));
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
                ->with($this->equalTo($bind_rdn), $this->equalTo($password))
                ->will($this->returnValue($expect));

        $this->assertEquals($expect, $this->zendLdapDriver->bind($user, $password));
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

    public function testBindUserInterfaceByUsernameSuccessful()
    {
        global $ldapServer;

        $username = 'username';
        $password = 'password';
        $user     = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');

        $user->expects($this->once())
                ->method('getUsername')
                ->will($this->returnValue($username));

        $ldapServer->expects($this->once())
                ->method('ldap_bind')
                ->with($this->equalTo($username), $this->equalTo($password))
                ->will($this->returnValue(true));

        $this->assertTrue($this->zendLdapDriver->bind($user, $password));
    }
}

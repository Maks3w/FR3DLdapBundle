<?php

namespace FR3D\LdapBundle\Tests\Driver;

use FR3D\LdapBundle\Driver\ZendLdapDriver;
use FR3D\LdapBundle\Tests\TestUser;
use Zend\Ldap\Exception\LdapException as ZendLdapException;
use Zend\Ldap\Ldap;

/**
 * Test class for ZendLdapDriver.
 */
class ZendLdapDriverMockedTest extends \PHPUnit_Framework_TestCase
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
        if (!function_exists('ldap_connect')) {
            $this->markTestSkipped('PHP LDAP extension not loaded');
        }

        $this->zend = $this->getMockBuilder('Zend\Ldap\Ldap')
                ->getMock();
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

        $this->zend->expects($this->once())
                ->method('searchEntries')
                ->with($this->equalTo($filter), $this->equalTo($baseDn), $this->equalTo(Ldap::SEARCH_SCOPE_SUB), $this->equalTo($attributes))
                ->will($this->returnValue(array($entry)));

        $this->assertEquals($expect, $this->zendLdapDriver->search($baseDn, $filter, $attributes));
    }

    public function testBindByUsernameSuccessful()
    {
        $username = 'test_username';
        $password = 'password';
        $user     = new TestUser();
        $user->setUsername($username);

        $this->zend->expects($this->once())
                ->method('bind')
                ->with($this->equalTo($username), $this->equalTo($password))
                ->will($this->returnValue($this->zend));

        $this->assertTrue($this->zendLdapDriver->bind($user, $password));
    }

    public function testBindByUsernameBadPassword()
    {
        $username = 'test_username';
        $password = 'bad_password';
        $user     = new TestUser();
        $user->setUsername($username);

        $this->zend->expects($this->once())
                ->method('bind')
                ->with($this->equalTo($username), $this->equalTo($password))
                ->will($this->throwException(new ZendLdapException($this->zend)));

        $this->assertFalse($this->zendLdapDriver->bind($user, $password));
    }

    public function testBindByUsernameBadUsername()
    {
        $username = 'bad_username';
        $password = 'bad_password';
        $user     = new TestUser();
        $user->setUsername($username);

        $this->zend->expects($this->once())
                ->method('bind')
                ->with($this->equalTo($username), $this->equalTo($password))
                ->will($this->throwException(new ZendLdapException($this->zend)));

        $this->assertFalse($this->zendLdapDriver->bind($user, $password));
    }

    public function testBindByDnSuccessful()
    {
        $dn       = 'uid=test_username,ou=example,dc=com';
        $password = 'password';
        $user     = new TestUser();
        $user->setDn($dn);

        $this->zend->expects($this->once())
                ->method('bind')
                ->with($this->equalTo($dn), $this->equalTo($password))
                ->will($this->returnValue($this->zend));

        $this->assertTrue($this->zendLdapDriver->bind($user, $password));
    }

    public function testBindByDnBadPassword()
    {
        $dn       = 'uid=test_username,ou=example,dc=com';
        $password = 'bad_password';
        $user     = new TestUser();
        $user->setDn($dn);

        $this->zend->expects($this->once())
                ->method('bind')
                ->with($this->equalTo($dn), $this->equalTo($password))
                ->will($this->throwException(new ZendLdapException($this->zend)));

        $this->assertFalse($this->zendLdapDriver->bind($user, $password));
    }

    public function testBindUserInterfaceByUsernameSuccessful()
    {
        $username = 'username';
        $password = 'password';
        $user     = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');

        $user->expects($this->once())
                ->method('getUsername')
                ->will($this->returnValue($username));

        $this->zend->expects($this->once())
                ->method('bind')
                ->with($this->equalTo($username), $this->equalTo($password))
                ->will($this->returnValue($this->zend));

        $this->assertTrue($this->zendLdapDriver->bind($user, $password));
    }
}

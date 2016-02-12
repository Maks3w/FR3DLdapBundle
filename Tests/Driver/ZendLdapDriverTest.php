<?php

namespace FR3D\LdapBundle\Tests\Driver;

use FR3D\LdapBundle\Driver\ZendLdapDriver;
use FR3D\Psr3MessagesAssertions\PhpUnit\TestLogger;
use Symfony\Component\Security\Core\User\UserInterface;
use Zend\Ldap\Exception\LdapException as ZendLdapException;
use Zend\Ldap\Ldap;

/**
 * Test class for ZendLdapDriver.
 */
class ZendLdapDriverTest extends \PHPUnit_Framework_TestCase
{
    use LdapDriverInterfaceTestTrait;

    /**
     * @var \Zend\Ldap\Ldap|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $zend;

    /**
     * Sets up the fixture, for example, opens a network Driver.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!function_exists('ldap_connect')) {
            $this->markTestSkipped('PHP LDAP extension not loaded');
        }

        $this->zend = $this->getMock('Zend\Ldap\Ldap');
        $this->driver = new ZendLdapDriver($this->zend, new TestLogger());
    }

    public function testSearch()
    {
        $baseDn = 'ou=example,dc=org';
        $filter = '(&(uid=test_username))';
        $attributes = ['uid'];

        $entry = [
            'dn' => 'uid=test_username,ou=example,dc=org',
            'uid' => ['test_username'],
        ];
        $expect = [
            'count' => 1,
            $entry,
        ];

        $this->zend->expects($this->once())
                ->method('searchEntries')
                ->with($this->equalTo($filter), $this->equalTo($baseDn), $this->equalTo(Ldap::SEARCH_SCOPE_SUB), $this->equalTo($attributes))
                ->will($this->returnValue([$entry]));

        self::assertEquals($expect, $this->driver->search($baseDn, $filter, $attributes));
    }

    /**
     * @dataProvider validUserPasswordProvider
     *
     * @param UserInterface $user
     * @param string $password
     * @param string $expectedBindRdn
     */
    public function testBindSuccessful(UserInterface $user, $password, $expectedBindRdn)
    {
        $this->zend->expects($this->once())
                ->method('bind')
                ->with($this->equalTo($expectedBindRdn), $this->equalTo($password))
                ->will($this->returnValue($this->zend));

        self::assertTrue($this->driver->bind($user, $password));
    }

    /**
     * @dataProvider invalidUserPasswordProvider
     *
     * @param UserInterface $user
     * @param string $password
     */
    public function testFailBindByDn(UserInterface $user, $password)
    {
        $this->zend->expects($this->once())
                ->method('bind')
                ->will($this->throwException(new ZendLdapException($this->zend)));

        self::assertFalse($this->driver->bind($user, $password));
    }
}

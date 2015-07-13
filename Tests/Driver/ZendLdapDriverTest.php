<?php

namespace FR3D\LdapBundle\Tests\Driver;

use FR3D\LdapBundle\Driver\ZendLdapDriver;
use FR3D\LdapBundle\Tests\TestUser;
use FR3D\Psr3MessagesAssertions\PhpUnit\TestLogger;
use phpmock\phpunit\PHPMock;
use Symfony\Component\Security\Core\User\UserInterface;
use Zend\Ldap\Ldap;

/**
 * Test class for ZendLdapDriver.
 */
class ZendLdapDriverTest extends AbstractLdapDriverTest
{
    use PHPMock;

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

        $ldapConnect = $this->getFunctionMock('Zend\Ldap', 'ldap_connect');
        $ldapConnect
            ->expects($this->any())->willReturnCallback(
                function () {
                    return ldap_connect();
                }
            )
        ;

        $ldapStartTls = $this->getFunctionMock('Zend\Ldap', 'ldap_start_tls');
        $ldapStartTls
            ->expects($this->any())
            ->will($this->returnValue(true))
        ;

        $this->zend = new Ldap($this->getOptions());
        $this->zendLdapDriver = new ZendLdapDriver($this->zend, new TestLogger());
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

        $this->zend = $this->getMock('Zend\Ldap\Ldap');
        $this->zendLdapDriver = new ZendLdapDriver($this->zend);
        $this->zend->expects($this->once())
                ->method('searchEntries')
                ->with($this->equalTo($filter), $this->equalTo($baseDn), $this->equalTo(Ldap::SEARCH_SCOPE_SUB), $this->equalTo($attributes))
                ->will($this->returnValue([$entry]));

        self::assertEquals($expect, $this->zendLdapDriver->search($baseDn, $filter, $attributes));
    }

    // Bind (bindRequireDn=false)
    /**
     * @dataProvider provideTestBind
     *
     * @param string $bind_rdn
     * @param string $password
     * @param bool $expect
     */
    public function testBind($bind_rdn, $password, $expect)
    {
        $user = new TestUser();
        $user->setUsername($bind_rdn);

        $ldapBind = $this->getFunctionMock('Zend\Ldap', 'ldap_bind');
        $ldapBind
            ->expects($this->once())
            ->with($this->anything(), $this->equalTo($bind_rdn), $this->equalTo($password))
            ->will($this->returnValue($expect))
        ;

        self::assertEquals($expect, $this->zendLdapDriver->bind($user, $password));
    }

    public function provideTestBind()
    {
        return [
            // Username
            ['test_username', 'password', true],
            ['bad_username', 'password', false],
            ['test_username', 'bad_password', false],
            // DN
            ['uid=test_username,ou=example,dc=com', 'password', true],
            ['uid=bad_username,ou=example,dc=com', 'password', false],
            ['uid=test_username,ou=example,dc=com', 'bad_password', false],
        ];
    }

    public function testBindUserInterfaceByUsernameSuccessful()
    {
        $username = 'username';
        $password = 'password';
        /** @var UserInterface|\PHPUnit_Framework_MockObject_MockObject $user */
        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');

        $user->expects($this->once())
                ->method('getUsername')
                ->will($this->returnValue($username));

        $ldapBind = $this->getFunctionMock('Zend\Ldap', 'ldap_bind');
        $ldapBind
            ->expects($this->once())
            ->with($this->anything(), $this->equalTo($username), $this->equalTo($password))
            ->will($this->returnValue(true))
        ;

        self::assertTrue($this->zendLdapDriver->bind($user, $password));
    }
}

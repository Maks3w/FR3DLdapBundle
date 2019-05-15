<?php

namespace FR3D\LdapBundle\Tests\Security\User;

use FR3D\LdapBundle\Security\User\LdapUserProvider;
use FR3D\LdapBundle\Tests\TestUser;
use FR3D\Psr3MessagesAssertions\PhpUnit\TestLogger;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use FR3D\LdapBundle\Ldap\LdapManager;

/**
 * @covers \FR3D\LdapBundle\Security\User\LdapUserProvider
 */
class LdapUserProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LdapManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ldapManager;

    /**
     * @var LdapUserProvider
     */
    protected $userProvider;

    protected function setUp(): void
    {
        $this->ldapManager = $this->getMockBuilder(LdapManager::class)
                ->disableOriginalConstructor()
                ->getMock();

        $this->userProvider = new LdapUserProvider($this->ldapManager, new TestLogger());
    }

    public function testLoadUserByUsername(): void
    {
        $username = 'test_username';
        $user = new TestUser();
        $user->setUsername($username);

        $this->ldapManager->expects($this->once())
                ->method('findUserByUsername')
                ->with($this->equalTo($username))
                ->will($this->returnValue($user));

        self::assertEquals($username, $this->userProvider->loadUserByUsername($username)->getUsername());
    }

    public function testLoadUserByUsernameNotFound(): void
    {
        $username = 'invalid_username';

        $this->ldapManager->expects($this->once())
                ->method('findUserByUsername')
                ->will($this->returnValue(null));

        try {
            $this->userProvider->loadUserByUsername($username);
            self::fail('Expected Symfony\Component\Security\Core\Exception\UsernameNotFoundException to be thrown');
        } catch (UsernameNotFoundException $notFoundException) {
            self::assertEquals($username, $notFoundException->getUsername());
        }
    }

    public function testRefreshUser(): void
    {
        $username = 'test_username';
        $user = new TestUser();
        $user->setUsername($username);

        $this->ldapManager->expects($this->once())
                ->method('findUserByUsername')
                ->with($this->equalTo($username))
                ->will($this->returnValue($user));

        self::assertEquals($user, $this->userProvider->refreshUser($user));
    }
}

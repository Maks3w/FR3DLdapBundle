<?php

namespace FR3D\LdapBundle\Tests\Security\User;

use FR3D\LdapBundle\Security\User\LdapUserProvider;
use FR3D\LdapBundle\Tests\TestUser;
use FR3D\Psr3MessagesAssertions\PhpUnit\TestLogger;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use FR3D\LdapBundle\Ldap\LdapManager;

/**
 * @covers \FR3D\LdapBundle\Security\User\LdapUserProvider
 */
class LdapUserProviderTest extends TestCase
{
    /**
     * @var LdapManager|MockObject
     */
    protected $ldapManager;

    /**
     * @var LdapUserProvider
     */
    protected $userProvider;

    protected function setUp(): void
    {
        $this->ldapManager = $this->createMock(LdapManager::class);

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
                ->willReturn($user);

        self::assertEquals($username, $this->userProvider->loadUserByUsername($username)->getUsername());
    }

    public function testLoadUserByUsernameNotFound(): void
    {
        $username = 'invalid_username';

        $this->ldapManager->expects($this->once())
                ->method('findUserByUsername')
                ->willReturn(null);

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
                ->willReturn($user);

        self::assertEquals($user, $this->userProvider->refreshUser($user));
    }
}

<?php

namespace FR3D\LdapBundle\Tests\Security\Authentication;

use Exception;
use FR3D\LdapBundle\Security\Authentication\LdapAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @covers FR3D\LdapBundle\Security\Authentication\LdapAuthenticationProvider
 */
class LdapAuthenticationProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LdapAuthenticationProvider
     */
    protected $ldapAuthenticationProvider;

    /**
     * @var \Symfony\Component\Security\Core\User\UserProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userProvider;

    /**
     * @var \FR3D\LdapBundle\Ldap\LdapManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ldapManager;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        /** @var UserCheckerInterface|\PHPUnit_Framework_MockObject_MockObject $userChecker */
        $userChecker = $this->getMock('Symfony\Component\Security\Core\User\UserCheckerInterface');
        $providerKey = 'provider_key';
        $this->userProvider = $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $this->ldapManager = $this->getMock('FR3D\LdapBundle\Ldap\LdapManagerInterface');
        $hideUserNotFoundExceptions = false;

        $this->ldapAuthenticationProvider = new LdapAuthenticationProvider($userChecker, $providerKey, $this->userProvider, $this->ldapManager, $hideUserNotFoundExceptions);
    }

    /**
     * @dataProvider validTokensProvider
     *
     * @param string $username
     * @param string $password
     */
    public function testAuthenticate($username, $password)
    {
        $user = $this->createUserMock();
        $token = $this->createToken($username, $password);

        $this->willRetrieveUser($username, $user);
        $this->willBind($user, $password);

        $authenticatedToken = $this->ldapAuthenticationProvider->authenticate($token);

        $this->assertValidAuthenticatedToken($authenticatedToken, $user);
    }

    public function validTokensProvider()
    {
        return [
            'normal' => ['test_username', 'password'],
            'password_0' => ['test_username', '0'],
        ];
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testRetrieveUserNotFound()
    {
        $username = 'notfound_username';
        $token = $this->createToken($username, 'password');

        $this->willRetrieveUser($username, new UsernameNotFoundException(''));

        $this->ldapAuthenticationProvider->authenticate($token);
    }

    public function testRetrieveUserUnexpectedError()
    {
        $username = 'username';
        $token = $this->createToken($username, 'password');

        $this->willRetrieveUser($username, new Exception(''));

        try {
            $this->ldapAuthenticationProvider->authenticate($token);
            self::fail('Expected Symfony\Component\Security\Core\Exception\AuthenticationServiceException to be thrown');
        } catch (AuthenticationServiceException $authenticationException) {
            self::assertEquals($token, $authenticationException->getToken());
        }
    }

    public function testRetrieveUserReturnsUserFromTokenOnReauthentication()
    {
        $user = $this->createUserMock();
        $password = 'password';
        $token = $this->createToken($user, $password);

        $this->userProvider->expects($this->never())
            ->method('loadUserByUsername');

        $this->willBind($user, $password);

        $authenticatedToken = $this->ldapAuthenticationProvider->authenticate($token);

        $this->assertValidAuthenticatedToken($authenticatedToken, $user);
    }

    public function testCheckAuthenticationWhenTokenNeedsReauthenticationWorksWithoutOriginalCredentials()
    {
        $password = 'password';
        $user = $this->createUserMock();

        $token = $this->createToken($user, $password);

        $this->willBind($user, $password);

        $authenticatedToken = $this->ldapAuthenticationProvider->authenticate($token);

        $this->assertValidAuthenticatedToken($authenticatedToken, $user);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     * @expectedExceptionMessage The password in the token is empty. You may forgive turn off `erase_credentials` in your `security.yml`
     */
    public function testCheckAuthenticationKnownUserCredentialsAreErased()
    {
        $password = '';
        $user = $this->createUserMock();

        $token = $this->createToken($user, $password);

        $this->ldapAuthenticationProvider->authenticate($token);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     * @expectedExceptionMessage The credentials were changed from another session.
     */
    public function testCheckAuthenticationKnownUserCredentialsChanged()
    {
        $password = 'other_password';
        $user = $this->createUserMock();

        $token = $this->createToken($user, $password);

        $this->willBind($user, $password, false);

        $this->ldapAuthenticationProvider->authenticate($token);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     * @expectedExceptionMessage The presented password is invalid.
     */
    public function testCheckAuthenticationUnknownUserBadCredentials()
    {
        $username = 'test_username';
        $password = 'bad_password';
        $user = $this->createUserMock();
        $token = $this->createToken($username, $password);

        $this->willRetrieveUser($username, $user);
        $this->willBind($user, $password, false);

        $this->ldapAuthenticationProvider->authenticate($token);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     * @expectedExceptionMessage The presented password cannot be empty.
     */
    public function testCheckAuthenticationUnknownUserPasswordEmpty()
    {
        $username = 'test_username';
        $password = '';
        $user = $this->createUserMock();

        $this->willRetrieveUser($username, $user);
        $token = $this->createToken($username, $password);

        $this->ldapAuthenticationProvider->authenticate($token);
    }

    /**
     * @return UserInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createUserMock()
    {
        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $user->expects($this->any())
            ->method('getRoles')
            ->willReturn([]);

        return $user;
    }

    /**
     * @param TokenInterface $authenticatedToken
     * @param UserInterface $expectedUser
     *
     * @return void
     */
    private function assertValidAuthenticatedToken($authenticatedToken, UserInterface $expectedUser)
    {
        self::assertInstanceOf(
            'Symfony\Component\Security\Core\Authentication\Token\TokenInterface',
            $authenticatedToken
        );

        self::assertEquals($expectedUser, $authenticatedToken->getUser());
        //self::assertTrue($authenticatedToken->isAuthenticated());
    }

    /**
     * @param UserInterface|string|object $user
     * @param string $credentials
     *
     * @return UsernamePasswordToken
     */
    private function createToken($user, $credentials)
    {
        return new UsernamePasswordToken($user, $credentials, 'provider_key');
    }

    /**
     * @param UserInterface $user
     * @param string $password
     * @param bool $result
     */
    private function willBind(UserInterface $user, $password, $result = true)
    {
        $this->ldapManager->expects($this->once())
            ->method('bind')
            ->with($this->equalTo($user), $this->equalTo($password))
            ->will($this->returnValue($result))
        ;
    }

    /**
     * @param string $username
     * @param UserInterface|Exception $userOrException
     *
     * @return void
     */
    private function willRetrieveUser($username, $userOrException)
    {
        $mock = $this->userProvider->expects($this->atMost(1))
            ->method('loadUserByUsername')
            ->with($this->equalTo($username))
        ;

        if ($userOrException instanceof Exception) {
            $mock->will($this->throwException($userOrException));
        } else {
            $mock->will($this->returnValue($userOrException));
        }
    }
}

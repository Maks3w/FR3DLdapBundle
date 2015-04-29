<?php

namespace FR3D\LdapBundle\Tests\Security\Authentication;

use FR3D\LdapBundle\Security\Authentication\LdapAuthenticationProvider;
use FR3D\LdapBundle\Tests\TestUser;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

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
        $userChecker                = $this->getMock('Symfony\Component\Security\Core\User\UserCheckerInterface');
        $providerKey                = 'provider_key';
        $this->userProvider         = $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $this->ldapManager          = $this->getMock('FR3D\LdapBundle\Ldap\LdapManagerInterface');
        $hideUserNotFoundExceptions = false;

        $this->ldapAuthenticationProvider = new LdapAuthenticationProvider($userChecker, $providerKey, $this->userProvider, $this->ldapManager, $hideUserNotFoundExceptions);
    }

    public function testRetrieveUser()
    {
        $method   = $this->setMethodAccessible('retrieveUser');
        $username = 'test_username';
        $user     = new TestUser();
        $token    = new UsernamePasswordToken($username, 'password', 'provider_key', array());

        $this->userProvider->expects($this->once())
                ->method('loadUserByUsername')
                ->with($this->equalTo($username))
                ->will($this->returnValue($user));

        $this->assertEquals($user, $method->invoke($this->ldapAuthenticationProvider, $username, $token));
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testRetrieveUserNotFound()
    {
        $method   = $this->setMethodAccessible('retrieveUser');
        $username = 'notfound_username';
        $token    = new UsernamePasswordToken($username, 'password', 'provider_key', array());

        $this->userProvider->expects($this->once())
                ->method('loadUserByUsername')
                ->with($this->equalTo($username))
                ->will($this->throwException(new UsernameNotFoundException('')));

        $method->invoke($this->ldapAuthenticationProvider, $username, $token);
    }

    public function testRetrieveUserUnexpectedError()
    {
        $method   = $this->setMethodAccessible('retrieveUser');
        $username = 'username';
        $token    = new UsernamePasswordToken($username, 'password', 'provider_key', array());

        $this->userProvider->expects($this->once())
                ->method('loadUserByUsername')
                ->with($this->equalTo($username))
                ->will($this->throwException(new \Exception('')));

        try {
            $method->invoke($this->ldapAuthenticationProvider, $username, $token);
            $this->fail('Expected Symfony\Component\Security\Core\Exception\AuthenticationServiceException to be thrown');
        } catch (AuthenticationServiceException $authenticationException) {
            $this->assertEquals($token, $authenticationException->getToken());
        }
    }

    public function testRetrieveUserReturnsUserFromTokenOnReauthentication()
    {
        $method = $this->setMethodAccessible('retrieveUser');

        $this->userProvider->expects($this->never())
            ->method('loadUserByUsername');

        $user = $this->getMock('Symfony\\Component\\Security\\Core\\User\\UserInterface');
        $token = new UsernamePasswordToken($user, '', 'provider_key', array());

        $result = $method->invoke(
            $this->ldapAuthenticationProvider,
            null,
            $token
        );

        $this->assertSame($user, $result);
    }

    public function testCheckAuthenticationKnownUser()
    {
        $method   = $this->setMethodAccessible('checkAuthentication');
        $username = 'username';
        $password = 'password';
        $user     = new TestUser();
        $user->setUsername($username);

        $token    = new UsernamePasswordToken($username, $password, 'provider_key', array());
        $token->setUser($user);

        $this->ldapManager->expects($this->once())
                ->method('bind')
                ->with($this->equalTo($user), $this->equalTo($password))
                ->will($this->returnValue(true));

        $method->invoke($this->ldapAuthenticationProvider, $user, $token);

        $this->assertTrue(true);
    }

    public function testCheckAuthenticationWhenTokenNeedsReauthenticationWorksWithoutOriginalCredentials()
    {
        $method = $this->setMethodAccessible('checkAuthentication');
        $username = 'username';
        $password = 'password';
        $user = new TestUser();
        $user->setUsername($username);

        $token = new UsernamePasswordToken($user, $password, 'provider_key', array());

        $this->ldapManager->expects($this->once())
            ->method('bind')
            ->with($this->equalTo($user), $this->equalTo($password))
            ->will($this->returnValue(true));

        $method->invoke(
            $this->ldapAuthenticationProvider,
            $this->getMock('Symfony\\Component\\Security\\Core\\User\\UserInterface'),
            $token
        );
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     * @expectedExceptionMessage The credentials were changed from another session.
     */
    public function testCheckAuthenticationKnownUserCredentialsChanged()
    {
        $method   = $this->setMethodAccessible('checkAuthentication');
        $username = 'bad_username';
        $password = 'other_password';
        $user     = new TestUser();
        $user->setUsername($username);

        $token    = new UsernamePasswordToken($username, $password, 'provider_key', array());
        $token->setUser($user);

        $this->ldapManager->expects($this->once())
                ->method('bind')
                ->with($this->equalTo($user), $this->equalTo($password))
                ->will($this->returnValue(false));

        $method->invoke($this->ldapAuthenticationProvider, $user, $token);
    }

    public function testCheckAuthenticationUnknownUser()
    {
        $method   = $this->setMethodAccessible('checkAuthentication');
        $username = 'test_username';
        $password = 'password';
        $user     = new TestUser();
        $user->setUsername($username);
        $token    = new UsernamePasswordToken($username, $password, 'provider_key', array());

        $this->ldapManager->expects($this->once())
                ->method('bind')
                ->with($this->equalTo($user), $this->equalTo($password))
                ->will($this->returnValue(true));

        $method->invoke($this->ldapAuthenticationProvider, $user, $token);

        $this->assertTrue(true);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     * @expectedExceptionMessage The presented password is invalid.
     */
    public function testCheckAuthenticationUnknownUserBadCredentials()
    {
        $method   = $this->setMethodAccessible('checkAuthentication');
        $username = 'test_username';
        $password = 'bad_password';
        $user     = new TestUser();
        $user->setUsername($username);
        $token    = new UsernamePasswordToken($username, $password, 'provider_key', array());

        $this->ldapManager->expects($this->once())
                ->method('bind')
                ->with($this->equalTo($user), $this->equalTo($password))
                ->will($this->returnValue(false));

        $method->invoke($this->ldapAuthenticationProvider, $user, $token);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     * @expectedExceptionMessage The presented password cannot be empty.
     */
    public function testCheckAuthenticationUnknownUserPasswordEmpty()
    {
        $method   = $this->setMethodAccessible('checkAuthentication');
        $username = 'test_username';
        $password = '';
        $user     = new TestUser();
        $user->setUsername($username);

        $token = new UsernamePasswordToken($username, $password, 'provider_key', array());

        $method->invoke($this->ldapAuthenticationProvider, $user, $token);
    }

    public function testCheckAuthenticationUnknownUserPasswordIs0()
    {
        $method   = $this->setMethodAccessible('checkAuthentication');
        $username = 'test_username';
        $password = '0';
        $user     = new TestUser();
        $user->setUsername($username);

        $token = new UsernamePasswordToken($username, $password, 'provider_key', array());

        $this->ldapManager->expects($this->once())
            ->method('bind')
            ->with($this->equalTo($user), $this->equalTo($password))
            ->will($this->returnValue(true));

        $method->invoke($this->ldapAuthenticationProvider, $user, $token);

        $this->assertTrue(true);
    }

    private function setMethodAccessible($name)
    {
        $reflectionClass = new \ReflectionClass('FR3D\LdapBundle\Security\Authentication\LdapAuthenticationProvider');
        $method          = $reflectionClass->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }
}

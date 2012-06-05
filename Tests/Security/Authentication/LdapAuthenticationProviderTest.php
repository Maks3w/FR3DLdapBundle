<?php

namespace FR3D\LdapBundle\Tests\Security\Authentication;

use FR3D\LdapBundle\Security\Authentication\LdapAuthenticationProvider;
use FR3D\LdapBundle\Tests\TestUser;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
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
     * @expectedException Symfony\Component\Security\Core\Exception\UsernameNotFoundException
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

    public function testCheckAuthenticationKnownUser()
    {
        $method   = $this->setMethodAccessible('checkAuthentication');
        $username = 'bad_username';
        $password = 'password';
        $user     = new TestUser();
        $user->setUsername($username);
        $user->setPassword($password);
        $token    = new UsernamePasswordToken($username, $password, 'provider_key', array());
        $token->setUser($user);

        $this->ldapManager->expects($this->once())
                ->method('bind')
                ->with($this->equalTo($user), $this->equalTo($password))
                ->will($this->returnValue(true));

        $method->invoke($this->ldapAuthenticationProvider, $user, $token);

        $this->assertTrue(true);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function testCheckAuthenticationKnownUserCredentialsChanged()
    {
        $method   = $this->setMethodAccessible('checkAuthentication');
        $username = 'bad_username';
        $password = 'other_password';
        $user     = new TestUser();
        $user->setUsername($username);
        $user->setPassword($password);
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
                ->method('findUserByUsername')
                ->with($this->equalTo($username))
                ->will($this->returnValue($user));
        $this->ldapManager->expects($this->once())
                ->method('bind')
                ->with($this->equalTo($user), $this->equalTo($password))
                ->will($this->returnValue(true));

        $method->invoke($this->ldapAuthenticationProvider, $user, $token);

        $this->assertTrue(true);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testCheckAuthenticationUnknownUserUsernameNotFound()
    {
        $method   = $this->setMethodAccessible('checkAuthentication');
        $username = 'bad_username';
        $password = 'password';
        $user     = new TestUser();
        $user->setUsername($username);
        $token    = new UsernamePasswordToken($username, $password, 'provider_key', array());

        $this->ldapManager->expects($this->once())
                ->method('findUserByUsername')
                ->with($this->equalTo($username))
                ->will($this->throwException(new UsernameNotFoundException('')));

        $method->invoke($this->ldapAuthenticationProvider, $user, $token);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\BadCredentialsException
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
                ->method('findUserByUsername')
                ->with($this->equalTo($username))
                ->will($this->returnValue($user));
        $this->ldapManager->expects($this->once())
                ->method('bind')
                ->with($this->equalTo($user), $this->equalTo($password))
                ->will($this->returnValue(false));

        $method->invoke($this->ldapAuthenticationProvider, $user, $token);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function testCheckAuthenticationUnknownUserPasswordEmpty()
    {
        $method   = $this->setMethodAccessible('checkAuthentication');
        $username = 'test_username';
        $password = '';
        $user     = new TestUser();
        $user->setUsername($username);

        $token = new UsernamePasswordToken($username, $password, 'provider_key', array());

        $this->ldapManager->expects($this->once())
                ->method('findUserByUsername')
                ->with($this->equalTo($username))
                ->will($this->returnValue($user));

        $method->invoke($this->ldapAuthenticationProvider, $user, $token);
    }

    public function testCheckAuthenticationUnknownUserWithoutDn()
    {
        $method       = $this->setMethodAccessible('checkAuthentication');
        $username     = 'test_username';
        $password     = 'password';
        $userOriginal = new TestUser();
        $userOriginal->setUsername($username);

        $dn       = 'ou=group, dc=host, dc=foo';
        $userLdap = new TestUser();
        $userLdap->setDn($dn);

        $userHydrated = new TestUser();
        $userHydrated->setUsername($username);
        $userHydrated->setDn($dn);

        $token = new UsernamePasswordToken($username, $password, 'provider_key', array());

        $this->ldapManager->expects($this->once())
                ->method('findUserByUsername')
                ->with($this->equalTo($username))
                ->will($this->returnValue($userLdap));
        $this->ldapManager->expects($this->once())
                ->method('bind')
                ->with($this->equalTo($userHydrated), $this->equalTo($password))
                ->will($this->returnValue(true));

        $method->invoke($this->ldapAuthenticationProvider, $userOriginal, $token);

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
?>

<?php

namespace FR3D\LdapBundle\Tests\Driver;

use FR3D\LdapBundle\Driver\LdapDriverInterface;
use FR3D\LdapBundle\Model\LdapUserInterface;
use Maks3w\PhpUnitMethodsTrait\Framework\TestCaseTrait;
use PHPUnit_Framework_Assert as Assert;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Common test methods for any FR3D\LdapBundle\Driver\LdapDriverInterface implementation.
 */
trait LdapDriverInterfaceTestTrait
{
    use TestCaseTrait;

    /**
     * @var LdapDriverInterface
     */
    protected $driver;

    public function testImplementsHydratorInterface()
    {
        Assert::assertInstanceOf(LdapDriverInterface::class, $this->driver);
    }

    public function validUserPasswordProvider()
    {
        $goodDn = 'uid=test_username,ou=example,dc=com';
        $goodPassword = 'password';
        $goodUsername = 'test_username';

        return [
            // description  => [user, password, expected_bind_rdn]
            'by Username' => [$this->mockUserInterface(['getUsername' => $goodUsername]), $goodPassword, $goodUsername],
            'by Dn' => [$this->mockLdapUserInterface(['getDn' => $goodDn]), $goodPassword, $goodDn],
        ];
    }

    public function invalidUserPasswordProvider()
    {
        $goodUsername = 'test_username';
        $wrongUsername = 'bad_username';

        $goodDn = 'uid=test_username,ou=example,dc=com';
        $wrongDn = 'uid=bad_username,ou=example,dc=com';

        $goodPassword = 'password';
        $badPassword = 'bad_password';

        return [
            // description  => [user, password, expected_bind_rdn]
            'wrong username' => [$this->mockUserInterface(['getUsername' => $wrongUsername]), $goodPassword, $wrongUsername],
            'wrong password' => [$this->mockUserInterface(['getUsername' => $goodUsername]), $badPassword, $goodUsername],
            'wrong DN' => [$this->mockLdapUserInterface(['getDn' => $wrongDn]), $goodPassword, $wrongDn],
            'good DN, wrong password' => [$this->mockLdapUserInterface(['getDn' => $goodDn]), $badPassword, $goodDn],
        ];
    }

    /**
     * @param array $methodReturns
     *
     * @return UserInterface
     */
    private function mockUserInterface(array $methodReturns)
    {
        /** @var UserInterface|\PHPUnit_Framework_MockObject_MockObject $user */
        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');

        foreach ($methodReturns as $method => $return) {
            $user->expects(TestCase::any())
                ->method($method)
                ->will(TestCase::returnValue($return))
            ;
        }

        return $user;
    }

    /**
     * @param array $methodReturns
     *
     * @return UserInterface|LdapUserInterface
     */
    private function mockLdapUserInterface(array $methodReturns)
    {
        /** @var UserInterface|LdapUserInterface|\PHPUnit_Framework_MockObject_MockObject $user */
        $user = $this->getMock('FR3D\LdapBundle\Tests\TestUser');

        foreach ($methodReturns as $method => $return) {
            $user->expects(TestCase::any())
                ->method($method)
                ->will(TestCase::returnValue($return))
            ;
        }

        return $user;
    }
}

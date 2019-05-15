<?php

namespace FR3D\LdapBundle\Tests\Driver;

use FR3D\LdapBundle\Driver\LdapDriverInterface;
use FR3D\LdapBundle\Model\LdapUserInterface;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\User\UserInterface;
use FR3D\LdapBundle\Tests\TestUser;

/**
 * Common test methods for any FR3D\LdapBundle\Driver\LdapDriverInterface implementation.
 */
trait LdapDriverInterfaceTestTrait
{
    /**
     * @var LdapDriverInterface
     */
    protected $driver;

    public function testImplementsHydratorInterface(): void
    {
        Assert::assertInstanceOf(LdapDriverInterface::class, $this->driver);
    }

    public function validUserPasswordProvider(): array
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

    public function invalidUserPasswordProvider(): array
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

    private function mockUserInterface(array $methodReturns): UserInterface
    {
        /** @var UserInterface|MockObject $user */
        $user = $this->createMock(UserInterface::class);

        foreach ($methodReturns as $method => $return) {
            $user
                ->method($method)
                ->willReturn($return)
            ;
        }

        return $user;
    }

    /**
     * @return UserInterface|LdapUserInterface
     */
    private function mockLdapUserInterface(array $methodReturns)
    {
        /** @var UserInterface|LdapUserInterface|MockObject $user */
        $user = $this->createMock(TestUser::class);

        foreach ($methodReturns as $method => $return) {
            $user
                ->method($method)
                ->willReturn($return)
            ;
        }

        return $user;
    }
}

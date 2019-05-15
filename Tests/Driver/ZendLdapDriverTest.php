<?php

namespace FR3D\LdapBundle\Tests\Driver;

use FR3D\LdapBundle\Driver\ZendLdapDriver;
use FR3D\Psr3MessagesAssertions\PhpUnit\TestLogger;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Zend\Ldap\Exception\LdapException as ZendLdapException;
use Zend\Ldap\Ldap;
use Psr\Log\LoggerInterface;
use FR3D\LdapBundle\Exception\SanitizingException;

/**
 * Test class for ZendLdapDriver.
 */
class ZendLdapDriverTest extends TestCase
{
    use LdapDriverInterfaceTestTrait;

    /**
     * @var \Zend\Ldap\Ldap|MockObject
     */
    protected $zend;

    /**
     * Sets up the fixture, for example, opens a network Driver.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        if (!function_exists('ldap_connect')) {
            $this->markTestSkipped('PHP LDAP extension not loaded');
        }

        $this->zend = $this->createMock(Ldap::class);
        $this->driver = new ZendLdapDriver($this->zend, new TestLogger());
    }

    public function testSearch(): void
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
                ->willReturn([$entry]);

        self::assertEquals($expect, $this->driver->search($baseDn, $filter, $attributes));
    }

    /**
     * @dataProvider validUserPasswordProvider
     */
    public function testBindSuccessful(UserInterface $user, $password, $expectedBindRdn): void
    {
        $this->zend->expects($this->once())
                ->method('bind')
                ->with($this->equalTo($expectedBindRdn), $this->equalTo($password))
                ->willReturn($this->zend);

        self::assertTrue($this->driver->bind($user, $password));
    }

    /**
     * @dataProvider invalidUserPasswordProvider
     */
    public function testFailBindByDn(UserInterface $user, $password): void
    {
        $this->zend->expects($this->once())
                ->method('bind')
                ->will($this->throwException(new ZendLdapException($this->zend)));

        self::assertFalse($this->driver->bind($user, $password));
    }

    public function testZendExceptionHandler(): void
    {
        $password = 'veryverysecret';

        $loggerMock = $this->createMock(LoggerInterface::class);

        $zendLdapExceptionMock = $this->createMock(ZendLdapException::class);
        $zendLdapExceptionMock
        ->method('__toString')
        ->willReturn("Zend\Ldap\Exception\LdapException: fr3d/ldap-bundle/Driver/ZendLdapDriver.php(82): Zend\Ldap\Ldap->bind('fogs', '$password')")
        ;

        $loggerMock->method('debug')->with('{exception}', $this->callback(function ($context) use ($password) {
            if (!array_key_exists('exception', $context)) {
                return $this->fail('Logger context must contain key "exception"');
            }
            if (!$context['exception'] instanceof SanitizingException) {
                return $this->fail('Logger context "exception" must contain object of class SanitizingException');
            }
            if (false !== strpos($context['exception']->__toString(), $password)) {
                return $this->fail('String representation of the SanitizingException must not contain the bind password');
            }

            return true;
        }));

        $reflectionClass = new \ReflectionClass($this->driver);
        $reflectionProperty = $reflectionClass->getProperty('logger');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->driver, $loggerMock);
        $reflectionMethod = $reflectionClass->getMethod('zendExceptionHandler');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($this->driver, $zendLdapExceptionMock, $password);
        self::assertTrue(true);
    }
}

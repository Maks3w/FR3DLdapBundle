<?php

namespace FR3D\LdapBundle\Tests\Ldap;

use FR3D\LdapBundle\Hydrator\HydratorInterface;
use FR3D\LdapBundle\Ldap\LdapManager;
use Symfony\Component\Security\Core\User\UserInterface;
use FR3D\LdapBundle\Driver\LdapDriverInterface;

/**
 * @covers \FR3D\LdapBundle\Ldap\LdapManager
 */
class LdapManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var array */
    protected $params;

    /**
     * @var \FR3D\LdapBundle\Driver\LdapDriverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $driver;

    /**
     * @var HydratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $hydrator;

    /**
     * @var LdapManager
     */
    protected $ldapManager;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->params = [
            'baseDn' => 'ou=Groups,dc=example,dc=com',
            'filter' => '(attr0=value0)',
            'usernameAttribute' => 'uid',
            'attributes' => [
            ],
        ];

        $this->driver = $this->getMock(LdapDriverInterface::class);

        $this->hydrator = $this->getMock(HydratorInterface::class);

        $this->ldapManager = new LdapManager($this->driver, $this->hydrator, $this->params);
    }

    /**
     * @covers \FR3D\LdapBundle\Ldap\LdapManager::findUserByUsername
     */
    public function testFindUserByUsername(): void
    {
        $username = 'test_username';

        $ldapResponse = $this->ldapResponse($username);

        $this->driver
            ->expects($this->once())
            ->method('search')
            ->with($this->equalTo('ou=Groups,dc=example,dc=com'),
                $this->equalTo('(&(attr0=value0)(uid=test_username))')
            )
            ->will($this->returnValue($ldapResponse))
        ;

        $resultUser = $this->ldapManager->findUserByUsername($username);

        self::assertEquals($username, $resultUser->getUsername());
    }

    /**
     * @covers \FR3D\LdapBundle\Ldap\LdapManager::findUserBy
     */
    public function testFindUserBy(): void
    {
        $username = 'test_username';

        $ldapResponse = $this->ldapResponse($username);

        $this->driver
            ->expects($this->once())
            ->method('search')
            ->with($this->equalTo('ou=Groups,dc=example,dc=com'),
                $this->equalTo('(&(attr0=value0)(uid=test_username))')
            )
            ->will($this->returnValue($ldapResponse))
        ;

        $criteria = ['uid' => 'test_username'];
        $resultUser = $this->ldapManager->findUserBy($criteria);

        self::assertEquals($username, $resultUser->getUsername());
    }

    /**
     * @covers \FR3D\LdapBundle\Ldap\LdapManager::buildFilter
     */
    public function testBuildFilter(): void
    {
        $reflectionClass = new \ReflectionClass(LdapManager::class);
        $method = $reflectionClass->getMethod('buildFilter');
        $method->setAccessible(true);

        $criteria = [
            'attr1' => 'value1',
            'attr2' => 'value2',
        ];
        $expected = '(&(attr0=value0)(attr1=value1)(attr2=value2))';

        self::assertEquals($expected, $method->invoke($this->ldapManager, $criteria));
    }

    /**
     * @covers \FR3D\LdapBundle\Ldap\LdapManager::bind
     */
    public function testBind(): void
    {
        $password = 'password';

        /** @var UserInterface $user */
        $user = $this->getMock(UserInterface::class);

        $this->driver->expects($this->once())
            ->method('bind')
            ->with($user, $this->equalTo($password))
            ->will($this->returnValue(true));

        self::assertTrue($this->ldapManager->bind($user, $password));
    }

    protected function ldapResponse(string $username): array
    {
        $entry = [
            'dn' => 'ou=group, dc=host, dc=foo',
            'uid' => [
                'count' => 1,
                0 => $username,
            ],
        ];

        $entries = [
            'count' => 1,
            $entry,
        ];

        $user = $this->getMock(UserInterface::class);
        $user->expects($this->any())
            ->method('getUsername')
            ->willReturn($username)
        ;

        $this->hydrator->expects($this->once())
            ->method('hydrate')
            ->with($entry)
            ->willReturn($user)
        ;

        return $entries;
    }
}

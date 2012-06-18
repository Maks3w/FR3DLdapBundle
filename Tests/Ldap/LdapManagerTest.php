<?php

namespace FR3D\LdapBundle\Tests\Ldap;

use FR3D\LdapBundle\Ldap\LdapManager;
use FR3D\LdapBundle\Tests\TestUser;

class LdapManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \FR3D\LdapBundle\Driver\LdapConnectionInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $connection;

    /**
     * @var \FR3D\LdapBundle\Model\UserManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userManager;

    /**
     * @var LdapManager
     */
    protected $ldapManager;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $params = array(
            'baseDn'     => 'ou=Groups,dc=example,dc=com',
            'filter'     => '(attr0=value0)',
            'attributes' => array(
                array(
                    'ldap_attr'   => 'uid',
                    'user_method' => 'setUsername',
                ),
            ),
        );

        $this->connection = $this->getMock('FR3D\LdapBundle\Driver\LdapConnectionInterface');

        $this->userManager = $this->getMock('FR3D\LdapBundle\Model\UserManagerInterface');
        $this->userManager->expects($this->any())
                ->method('createUser')
                ->will($this->returnValue(new TestUser()));

        $this->ldapManager = new LdapManager($this->connection, $this->userManager, $params);
    }

    /**
     * @covers FR3D\LdapBundle\Ldap\LdapManager::__construct
     */
    public function testConstruct()
    {
        $params = array(
            'baseDn'     => 'ou=Groups,dc=example,dc=com',
            'filter'     => '(attr0=value0)',
            'attributes' => array(
                array(
                    'ldap_attr'   => 'uid',
                    'user_method' => 'setUsername',
                ),
                array(
                    'ldap_attr'   => 'mail',
                    'user_method' => 'setEmail',
                ),
            ),
        );

        $this->ldapManager = new LdapManager($this->connection, $this->userManager, $params);

        $reflectionClass        = new \ReflectionClass('FR3D\LdapBundle\Ldap\LdapManager');
        $propertyLdapAttributes = $reflectionClass->getProperty('ldapAttributes');
        $propertyLdapAttributes->setAccessible(true);

        $propertyLdapUsernameAttr = $reflectionClass->getProperty('ldapUsernameAttr');
        $propertyLdapUsernameAttr->setAccessible(true);

        $this->assertEquals(array('uid', 'mail'), $propertyLdapAttributes->getValue($this->ldapManager));
        $this->assertEquals('uid', $propertyLdapUsernameAttr->getValue($this->ldapManager));
    }

    /**
     * @covers FR3D\LdapBundle\Ldap\LdapManager::findUserByUsername
     */
    public function testFindUserByUsername()
    {
        $username = 'test_username';

        $entry = array('count' => 1, array('dn'  => 'ou=group, dc=host, dc=foo', 'uid' => array('test_username')));

        $this->connection->expects($this->once())
                ->method('search')
                ->with($this->equalTo('ou=Groups,dc=example,dc=com'), $this->equalTo('(&(attr0=value0)(uid=test_username))'), $this->equalTo(array('uid')))
                ->will($this->returnValue($entry));

        $resultUser = $this->ldapManager->findUserByUsername($username);

        $this->assertEquals($username, $resultUser->getUsername());
    }

    /**
     * @covers FR3D\LdapBundle\Ldap\LdapManager::findUserBy
     */
    public function testFindUserBy()
    {
        $user = new TestUser();
        $user->setUsername('test_username');

        $entry = array('count' => 1, array('dn'  => 'ou=group, dc=host, dc=foo', 'uid' => array('test_username')));

        $this->connection->expects($this->once())
                ->method('search')
                ->with($this->equalTo('ou=Groups,dc=example,dc=com'), $this->equalTo('(&(attr0=value0)(uid=test_username))'), $this->equalTo(array('uid')))
                ->will($this->returnValue($entry));

        $criteria = array('uid'       => 'test_username');
        $resultUser = $this->ldapManager->findUserBy($criteria);

        $this->assertEquals($user->getUsername(), $resultUser->getUsername());
    }

    /**
     * @covers FR3D\LdapBundle\Ldap\LdapManager::buildFilter
     */
    public function testBuildFilter()
    {
        $reflectionClass = new \ReflectionClass('FR3D\LdapBundle\Ldap\LdapManager');
        $method          = $reflectionClass->getMethod('buildFilter');
        $method->setAccessible(true);

        $criteria = array(
            'attr1'   => 'value1',
            'attr2'   => 'value2',
        );
        $expected = '(&(attr0=value0)(attr1=value1)(attr2=value2))';

        $this->assertEquals($expected, $method->invoke($this->ldapManager, $criteria));
    }

    /**
     * @covers FR3D\LdapBundle\Ldap\LdapManager::hydrate
     */
    public function testHydrate()
    {
        $reflectionClass = new \ReflectionClass('FR3D\LdapBundle\Ldap\LdapManager');
        $method          = $reflectionClass->getMethod('hydrate');
        $method->setAccessible(true);

        $user = new TestUser();

        $entry = array(
            'dn'  => 'ou=group, dc=host, dc=foo',
            'uid' => array('test_username')
        );

        $method->invoke($this->ldapManager, $user, $entry);

        $this->assertEquals('test_username', $user->getUsername());
        $this->assertTrue($user->isEnabled());
    }

    /**
     * @covers FR3D\LdapBundle\Ldap\LdapManager::hydrate
     */
    public function testHydrateArray()
    {
        $params = array(
            'baseDn'     => 'ou=Groups,dc=example,dc=com',
            'filter'     => '(attr0=value0)',
            'attributes' => array(
                array(
                    'ldap_attr'   => 'roles',
                    'user_method' => 'setRoles',
                )
            ),
        );

        $this->ldapManager = new LdapManager($this->connection, $this->userManager, $params);

        $reflectionClass = new \ReflectionClass('FR3D\LdapBundle\Ldap\LdapManager');
        $method          = $reflectionClass->getMethod('hydrate');
        $method->setAccessible(true);

        $user = new TestUser();
        $roles = array(
            'count' => 3,
            0 => 'ROLE1',
            1 => 'ROLE2',
            2 => 'ROLE3'
        );

        $entry = array(
            'dn'       => 'ou=group, dc=host, dc=foo',
            'roles'    => $roles,
        );

        $method->invoke($this->ldapManager, $user, $entry);

        $this->assertEquals(array_slice($roles, 1), $user->getRoles());
        $this->assertTrue($user->isEnabled());
    }

    /**
     * @covers FR3D\LdapBundle\Ldap\LdapManager::bind
     */
    public function testBind()
    {
        $user = new TestUser();
        $user->setDn('dn=test_username');

        $this->connection->expects($this->once())
                ->method('bind')
                ->with($this->equalTo('dn=test_username'), $this->equalTo('password'))
                ->will($this->returnValue(true));

        $this->assertTrue($this->ldapManager->bind($user, 'password'));
    }

    public function testFilterEscapeBasicOperation()
    {
        $input    = 'a*b(b)d\e/f';
        $expected = 'a\2ab\28b\29d\5ce/f';
        $this->assertEquals($expected, LdapManager::escapeValue($input));
    }

    public function testEscapeValues()
    {
        $expected  = 't\28e,s\29t\2av\5cal\1eue';
        $filterval = 't(e,s)t*v\\al' . chr(30) . 'ue';
        $this->assertEquals($expected, LdapManager::escapeValue($filterval));
        $this->assertEquals($expected, LdapManager::escapeValue(array($filterval)));
        $this->assertEquals(array($expected, $expected, $expected), LdapManager::escapeValue(array($filterval, $filterval, $filterval)));
    }

    public function testUnescapeValues()
    {
        $expected  = 't(e,s)t*v\\al' . chr(30) . 'ue';
        $filterval = 't\28e,s\29t\2av\5cal\1eue';
        $this->assertEquals($expected, LdapManager::unescapeValue($filterval));
        $this->assertEquals($expected, LdapManager::unescapeValue(array($filterval)));
        $this->assertEquals(array($expected, $expected, $expected), LdapManager::unescapeValue(array($filterval, $filterval, $filterval)));
    }

    public function testFilterValueUtf8()
    {
        $filter    = 'ÄÖÜäöüß€';
        $escaped   = LdapManager::escapeValue($filter);
        $unescaped = LdapManager::unescapeValue($escaped);
        $this->assertEquals($filter, $unescaped);
    }
}

<?php

namespace FR3D\LdapBundle\Tests\Ldap;

use FR3D\LdapBundle\Ldap\LdapManager;
use FR3D\LdapBundle\Tests\TestUser;

class LdapManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \FR3D\LdapBundle\Driver\LdapConnectionInterface
     */
    protected $connection;

    /**
     * @var \FR3D\LdapBundle\Model\UserManagerInterface
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
}

<?php

namespace FR3D\LdapBundle\Tests\Ldap;

use FR3D\LdapBundle\Ldap\LdapManager;
use FR3D\LdapBundle\Model\LdapUser;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

class LdapManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var array */
    protected $params;

    /**
     * @var \FR3D\LdapBundle\Driver\LdapDriverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $driver;

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
        $this->params = array(
            'baseDn'     => 'ou=Groups,dc=example,dc=com',
            'filter'     => '(attr0=value0)',
            'attributes' => array(
                array(
                    'ldap_attr'   => 'uid',
                    'user_method' => 'setUsername',
                ),
            ),
        );

        $this->driver = $this->getMock('FR3D\LdapBundle\Driver\LdapDriverInterface');

        $this->userManager = $this->getMock('FR3D\LdapBundle\Model\UserManagerInterface');
        $this->userManager->expects($this->any())
            ->method('createUser')
            ->will($this->returnValue(new LdapUser()));

        $this->ldapManager = new LdapManager($this->driver, $this->userManager, $this->params);
    }

    /**
     * @covers FR3D\LdapBundle\Ldap\LdapManager::__construct
     */
    public function testConstruct()
    {
        $this->params['attributes'][] = array(
            'ldap_attr'   => 'mail',
            'user_method' => 'setEmail',
        );

        $this->ldapManager = new LdapManager($this->driver, $this->userManager, $this->params);

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

        $entries = array(
            'count' => 1,
            array(
                'dn'  => 'ou=group, dc=host, dc=foo',
                'uid' => array(
                    'count' => 1,
                    0       => $username,
                ),
            ),
        );

        $this->driver
            ->expects($this->once())
            ->method('search')
            ->with($this->equalTo('ou=Groups,dc=example,dc=com'),
                $this->equalTo('(&(attr0=value0)(uid=test_username))'),
                $this->equalTo(array('uid')))
            ->will($this->returnValue($entries));

        $resultUser = $this->ldapManager->findUserByUsername($username);

        $this->assertEquals($username, $resultUser->getUsername());
    }

    /**
     * @covers FR3D\LdapBundle\Ldap\LdapManager::findUserBy
     */
    public function testFindUserBy()
    {
        $username = 'test_username';

        $user = new LdapUser();
        $user->setUsername($username);

        $entries = array(
            'count' => 1,
            array(
                'dn'  => 'ou=group, dc=host, dc=foo',
                'uid' => array(
                    'count' => 1,
                    0       => $username,
                ),
            ),
        );

        $this->driver
            ->expects($this->once())
            ->method('search')
            ->with($this->equalTo('ou=Groups,dc=example,dc=com'),
                $this->equalTo('(&(attr0=value0)(uid=test_username))'),
                $this->equalTo(array('uid')))
            ->will($this->returnValue($entries));

        $criteria = array('uid' => 'test_username');
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
            'attr1' => 'value1',
            'attr2' => 'value2',
        );
        $expected = '(&(attr0=value0)(attr1=value1)(attr2=value2))';

        $this->assertEquals($expected, $method->invoke($this->ldapManager, $criteria));
    }

    /**
     * @covers FR3D\LdapBundle\Ldap\LdapManager::hydrate
     */
    public function testHydrate()
    {
        $username = 'test_username';

        $reflectionClass = new \ReflectionClass('FR3D\LdapBundle\Ldap\LdapManager');
        $method          = $reflectionClass->getMethod('hydrate');
        $method->setAccessible(true);

        $user = new LdapUser();

        $entry = array(
            'dn'    => 'ou=group, dc=host, dc=foo',
            'count' => 1,
            'uid'   => array(
                'count' => 1,
                0       => $username,
            ),
        );

        $method->invoke($this->ldapManager, $user, $entry);

        $this->assertEquals($username, $user->getUsername());
        if ($user instanceof AdvancedUserInterface) {
            $this->assertTrue($user->isEnabled());
        }
    }

    /**
     * @covers FR3D\LdapBundle\Ldap\LdapManager::hydrate
     */
    public function testDontTryToHydrateMissingAttributes()
    {
        $reflectionClass = new \ReflectionClass('FR3D\LdapBundle\Ldap\LdapManager');
        $method          = $reflectionClass->getMethod('hydrate');
        $method->setAccessible(true);

        $user = new LdapUser();

        $entry = array(
            'dn'    => 'ou=group, dc=host, dc=foo',
            'count' => 1,
        );

        $method->invoke($this->ldapManager, $user, $entry);

        $this->assertNull($user->getUsername());
    }

    /**
     * @covers FR3D\LdapBundle\Ldap\LdapManager::hydrate
     */
    public function testHydrateArray()
    {
        $this->params['attributes'] = array(
            array(
                'ldap_attr'   => 'roles',
                'user_method' => 'setRoles',
            ),
        );

        $user  = new LdapUser();
        $roles = array(
            'count' => 3,
            0 => 'ROLE1',
            1 => 'ROLE2',
            2 => 'ROLE3',
            3 => 'ROLE_USER',
        );

        $entry = array(
            'dn'    => 'ou=group, dc=host, dc=foo',
            'roles' => $roles,
        );

        $this->ldapManager = new LdapManager($this->driver, $this->userManager, $this->params);

        $reflectionClass = new \ReflectionClass('FR3D\LdapBundle\Ldap\LdapManager');
        $method          = $reflectionClass->getMethod('hydrate');
        $method->setAccessible(true);

        $method->invoke($this->ldapManager, $user, $entry);

        $this->assertEquals(array_slice($roles, 1), $user->getRoles());
        if ($user instanceof AdvancedUserInterface) {
            $this->assertTrue($user->isEnabled());
        }
    }

    /**
     * @covers FR3D\LdapBundle\Ldap\LdapManager::hydrateRoles
     */
    public function testHydrateRolesWithMemberofRole()
    {
        $username = 'test_username';
        $userDn = 'cn=Test Username, ou=group, dc=host, dc=foo';

        $this->params['role'] = array(
                'memberOf' => array(
                    'dnSuffixFilter' => 'ou=Roles,dc=example,dc=com',
                ),
        );

        $this->ldapManager = new LdapManager($this->driver, $this->userManager, $this->params);

        $reflectionClass = new \ReflectionClass('FR3D\LdapBundle\Ldap\LdapManager');
        $method = $reflectionClass->getMethod('hydrateRoles');
        $method->setAccessible(true);

        $entry = array(
            'dn' => $userDn,
            'count' => 1,
            'cn' => array(
                'count' => 1,
                0 => $username,
            ),
            'memberof' => array(
                0 => 'cn=Admin,ou=Roles,dc=example,dc=com',
            ),
        );
        $roles = array('ROLE_ADMIN', 'ROLE_USER');

        $user = new LdapUser();
        $user->setUsername($username);
        $user->setDn($userDn);

        $method->invoke($this->ldapManager, $user, $entry);

        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($roles, $user->getRoles());
    }

    /**
     * @covers FR3D\LdapBundle\Ldap\LdapManager::hydrateRoles
     */
    public function testHydrateRolesWithRoleSearch()
    {
        $username = 'test_username';
        $userDn = 'cn=Test Username, ou=group, dc=host, dc=foo';

        $this->params['role'] = array(
                'search' => array(
                    'baseDn' => 'ou=Roles,dc=example,dc=com',
                    'nameAttribute' => 'cn',
                    'userDnAttribute' => 'member',
                    'userId' => 'dn',
                ),
        );

        $user = new LdapUser();
        $user->setUsername($username);
        $user->setDn($userDn);

        $roleEntries = array(
            'count' => 1,
            array(
                'dn' => 'cn=Admin, ou=group, dc=host, dc=foo',
                'cn' => 'Admin',
                'member' => array(
                    'count' => 1,
                    0 => $userDn,
                ),
            ),
        );

        $this->driver
            ->expects($this->once())
            ->method('search')
            ->with($this->equalTo('ou=Roles,dc=example,dc=com'),
                $this->equalTo(sprintf('(&(member=%s))', $userDn)),
                $this->equalTo(array('cn')))
            ->will($this->returnValue($roleEntries));

        $this->ldapManager = new LdapManager($this->driver, $this->userManager, $this->params);

        $reflectionClass = new \ReflectionClass('FR3D\LdapBundle\Ldap\LdapManager');
        $method = $reflectionClass->getMethod('hydrateRoles');
        $method->setAccessible(true);

        $entry = array(
            'dn' => $userDn,
            'count' => 1,
            'cn' => array(
                'count' => 1,
                0 => $username,
            ),
        );
        $roles = array('ROLE_ADMIN', 'ROLE_USER');

        $method->invoke($this->ldapManager, $user, $entry);

        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($roles, $user->getRoles());
    }

    /**
     * @covers FR3D\LdapBundle\Ldap\LdapManager::bind
     */
    public function testBind()
    {
        $password = 'password';

        $user = new LdapUser();

        $this->driver->expects($this->once())
            ->method('bind')
            ->with($user, $this->equalTo($password))
            ->will($this->returnValue(true));

        $this->assertTrue($this->ldapManager->bind($user, $password));
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

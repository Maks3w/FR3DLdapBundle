<?php

namespace FR3D\LdapBundle\Tests\Hydrator;

use FR3D\LdapBundle\Hydrator\HydratorInterface;

abstract class AbstractHydratorTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    public function testHydrate()
    {
        $username = 'test_username';

        $entry = [
            'dn' => 'ou=group, dc=host, dc=foo',
            'count' => 1,
            'uid' => [
                'count' => 1,
                0 => $username,
            ],
        ];

        $user = $this->hydrator->hydrate($entry);

        $this->assertEquals($username, $user->getUsername());
    }

    public function testDontTryToHydrateMissingAttributes()
    {
        $entry = [
            'dn' => 'ou=group, dc=host, dc=foo',
            'count' => 1,
        ];
        $user = $this->hydrator->hydrate($entry);

        $this->assertNull($user->getUsername());
    }

    public function testHydrateArray()
    {
        $roles = [
            'count' => 3,
            0 => 'ROLE1',
            1 => 'ROLE2',
            2 => 'ROLE3',
        ];

        $entry = [
            'dn' => 'ou=group, dc=host, dc=foo',
            'roles' => $roles,
        ];

        $user = $this->hydrator->hydrate($entry);

        $this->assertEquals(array_slice($roles, 1), $user->getRoles());
    }

    public function testHydrateRolesWithMemberOfRole()
    {
        $username = 'test_username';
        $userDn = 'cn=Test Username, ou=group, dc=host, dc=foo';

        $this->params['role'] = array(
            'memberOf' => array(
                'dnSuffixFilter' => 'ou=Roles,dc=example,dc=com',
            ),
        );

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

        $user = $this->hydrator->hydrate($entry);

        $this->assertEquals($roles, $user->getRoles());
    }

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

        $user = $this->hydrator->hydrate($entry);

        $this->assertEquals($roles, $user->getRoles());
    }
}

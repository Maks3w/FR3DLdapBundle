<?php

namespace FR3D\LdapBundle\Tests\Model;

use FR3D\LdapBundle\Model\HydratorInterface;

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
        $this->assertTrue($user->isEnabled());
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
        $this->assertTrue($user->isEnabled());
    }
}

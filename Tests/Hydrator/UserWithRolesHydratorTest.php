<?php

namespace FR3D\LdapBundle\Tests\Hydrator;

use FR3D\LdapBundle\Hydrator\UserWithRolesHydrator;


class UserWithHydratorTest extends AbstractHydratorTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $params = [
            'user_class' => 'FR3D\LdapBundle\Model\LdapUser',
            ''
        ];

        $this->hydrator = new UserWithRolesHydrator($params, $this->getDefaultUserConfig());
    }
    
    public function testLdapSearch()
    {
        $params = [
            'role' => [ 
                'search' => [
                    'groupNameAttribute' => 'cn'
                ]
            ]
        ];

        $hydrator = new UserWithRolesHydrator($params, $this->getDefaultUserConfig());
        
        $roles = [
            'count' => 3,
            0 => 'ROLE1',
            1 => 'ROLE2',
            2 => 'ROLE3',
        ];

        $entry = [
            'dn' => 'ou=group, dc=host, dc=foo',
            'groups' => $roles,
        ];

        $user = $this->hydrator->hydrate($entry);

        $this->assertValidHydrateReturn($user);
        Assert::assertEquals(array_slice($roles, 1), $user->getRoles());
    }
}

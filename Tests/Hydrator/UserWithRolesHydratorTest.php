<?php

namespace FR3D\LdapBundle\Tests\Hydrator;

use FR3D\LdapBundle\Hydrator\UserWithRolesHydrator;
use PHPUnit_Framework_Assert as Assert;


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
            'user_class' => 'FR3D\LdapBundle\Model\LdapUser',
            'role' => [ 
                'search' => [
                    'groupNameAttribute' => 'cn'
                ]
            ]
        ];

        $hydrator = new UserWithRolesHydrator($params, $this->getDefaultUserConfig());
        
        $groups = [
            'count' => 2,
            0 => [
                'cn' => [
                    0 => 'ROLE1'
                ],
                'dn' => 'cn=ROLE2, ou=group, dc=host, dc=foo',
                'member' => [
                    0 => 'cn=test_username, ou=people, dc=host, dc=foo',
                ],
            ],
            1 => [
                'cn' => [
                    0 => 'ROLE2'
                ],
                'dn' => 'cn=ROLE2, ou=group, dc=host, dc=foo',
                'member' => [
                    0 => 'cn=test_username, ou=people, dc=host, dc=foo'
                ],
            ],
        ];
              
        $entry = [
            'dn' => 'cn=test_username, ou=people, dc=host, dc=foo',
            'groups' => $groups,
        ];
        
        $roles = [
            0 => 'ROLE_ROLE1',
            1 => 'ROLE_ROLE2',
        ];

        $user = $hydrator->hydrate($entry);

        $this->assertValidHydrateReturn($user);
        Assert::assertEquals($roles, $user->getRoles());
    }
    
    public function testMemberOf()
    {
        $params = [
            'user_class' => 'FR3D\LdapBundle\Model\LdapUser',
            'role' => [ 
                'memberOf' => [
                    'dnSuffixFilter' => 'ou=group,dc=host,dc=foo'
                ]
            ]
        ];

        $hydrator = new UserWithRolesHydrator($params, $this->getDefaultUserConfig());
        
        $memberOf = [
            'count' => 2,
            0 => 'cn=ROLE1,ou=group,dc=host,dc=foo',
            1 => 'cn=ROLE2,ou=group,dc=host,dc=foo',
        ];
              
        $entry = [
            'dn' => 'cn=test_username, ou=people, dc=host, dc=foo',
            'memberof' => $memberOf,
        ];
        
        $roles = [
            0 => 'ROLE_ROLE1',
            1 => 'ROLE_ROLE2',
        ];

        $user = $hydrator->hydrate($entry);

        $this->assertValidHydrateReturn($user);
        Assert::assertEquals($roles, $user->getRoles());
    }
}

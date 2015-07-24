<?php

namespace FR3D\LdapBundle\Tests\Hydrator;

use FR3D\LdapBundle\Hydrator\HydratorInterface;
use PHPUnit_Framework_Assert as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Common test methods for any FR3D\LdapBundle\Hydrator\HydratorInterface implementation.
 */
trait HydratorInterfaceTestTrait
{
    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    public function testImplementsHydratorInterface()
    {
        Assert::assertInstanceOf(HydratorInterface::class, $this->hydrator);
    }

    /**
     * @dataProvider validLdapUserAttributesProvider
     *
     * @param array $ldapEntry
     * @param array $methodsReturn
     */
    public function testHydrate(array $ldapEntry, array $methodsReturn)
    {
        $user = $this->hydrator->hydrate($ldapEntry);

        Assert::assertInstanceOf(UserInterface::class, $user);
        foreach ($methodsReturn as $method => $returnValue) {
            Assert::assertEquals($returnValue, $user->$method(), "UserInterface::{$method}() return value mismatch");
        }
    }

    /**
     * @return array
     */
    public function validLdapUserAttributesProvider()
    {
        return [
            // Description => [ldap entry, [UserInterfaceMethod => return value]]
            'hydrate single attributes' => [
                'ldap entry' => [
                    'dn' => 'ou=group, dc=host, dc=foo',
                    'count' => 1,
                    'uid' => [
                        'count' => 1,
                        0 => 'test_username',
                    ],
                ],
                'expected methods return' => [
                    'getUserName' => 'test_username',
                ],
            ],
            'hydrate attributes collections' => [
                'ldap entry' => [
                    'dn' => 'ou=group, dc=host, dc=foo',
                    'roles' => [
                        'count' => 2,
                        0 => 'ROLE1',
                        1 => 'ROLE2',
                    ],
                ],
                'expected methods return' => [
                    'getRoles' => [
                        0 => 'ROLE1',
                        1 => 'ROLE2',
                    ],
                ],
            ],
            'hydrate single attributes without count index' => [
                'ldap entry' => [
                    'dn' => 'ou=group, dc=host, dc=foo',
                    'uid' => [
                        0 => 'test_username',
                    ],
                ],
                'expected methods return' => [
                    'getUserName' => 'test_username',
                ],
            ],
            'hydrate attributes collections without count index' => [
                'ldap entry' => [
                    'dn' => 'ou=group, dc=host, dc=foo',
                    'roles' => [
                        0 => 'ROLE1',
                        1 => 'ROLE2',
                    ],
                ],
                'expected methods return' => [
                    'getRoles' => [
                        0 => 'ROLE1',
                        1 => 'ROLE2',
                    ],
                ],
            ],
            'hydrate attributes collections with only 1 element' => [
                'ldap entry' => [
                    'dn' => 'ou=group, dc=host, dc=foo',
                    'roles' => [
                        0 => 'ROLE1',
                    ],
                ],
                'expected methods return' => [
                    'getRoles' => [
                        0 => 'ROLE1',
                    ],
                ],
            ],
            'empty ldap entry return an empty user' => [
                'ldap entry' => [
                    'dn' => 'ou=group, dc=host, dc=foo',
                    'count' => 1,
                ],
                'expected methods return' => [
                    'getUserName' => null,
                ],
            ],
        ];
    }
}

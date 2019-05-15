<?php

namespace FR3D\LdapBundle\Tests\Hydrator;

use FR3D\LdapBundle\Hydrator\HydratorInterface;
use PHPUnit_Framework_Assert as Assert;

/**
 * Common test methods for any FR3D\LdapBundle\Hydrator\HydratorInterface implementation.
 */
trait HydratorInterfaceTestTrait
{
    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    public function testImplementsHydratorInterface(): void
    {
        Assert::assertInstanceOf(HydratorInterface::class, $this->hydrator);
    }

    /**
     * @dataProvider validLdapUserAttributesProvider
     */
    public function testHydrate(array $ldapEntry, array $methodsReturn): void
    {
        $user = $this->hydrator->hydrate($ldapEntry);

        foreach ($methodsReturn as $method => $returnValue) {
            Assert::assertEquals($returnValue, $user->$method(), "UserInterface::{$method}() return value mismatch");
        }
    }

    public function validLdapUserAttributesProvider(): array
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

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

    public function testImplementsHydratorInterface()
    {
        Assert::assertInstanceOf(HydratorInterface::class, $this->hydrator);
    }

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

        $this->assertValidHydrateReturn($user);
        Assert::assertEquals($username, $user->getUsername());
    }

    public function testDontTryToHydrateMissingAttributes()
    {
        $entry = [
            'dn' => 'ou=group, dc=host, dc=foo',
            'count' => 1,
        ];

        $user = $this->hydrator->hydrate($entry);

        $this->assertValidHydrateReturn($user);
        Assert::assertNull($user->getUsername());
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

        $this->assertValidHydrateReturn($user);
        Assert::assertEquals(array_slice($roles, 1), $user->getRoles());
    }

    /**
     * Assert hydrate() return follow interface return constraints.
     *
     * Assert return must be of type `Symfony\Component\Security\Core\User\UserInterface`
     *
     * @param mixed $hydrateReturn
     *
     * @return void
     */
    protected function assertValidHydrateReturn($hydrateReturn)
    {
        Assert::assertInstanceOf('Symfony\Component\Security\Core\User\UserInterface', $hydrateReturn);
    }
}

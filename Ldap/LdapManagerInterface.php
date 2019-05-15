<?php

namespace FR3D\LdapBundle\Ldap;

use Symfony\Component\Security\Core\User\UserInterface;

interface LdapManagerInterface
{
    /**
     * Find a user by its username.
     *
     * @return UserInterface|null The user or null if the user does not exist
     */
    public function findUserByUsername(string $username): ?UserInterface;

    /**
     * Finds one user by the given criteria.
     *
     * @return UserInterface|null The user or null if the user does not exist
     */
    public function findUserBy(array $criteria): ?UserInterface;

    /**
     * Bind the user on ldap.
     */
    public function bind(UserInterface $user, string $password): bool;
}

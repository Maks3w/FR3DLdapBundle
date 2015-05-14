<?php

namespace FR3D\LdapBundle\Ldap;

use Symfony\Component\Security\Core\User\UserInterface;

interface LdapManagerInterface
{
    /**
     * Find a user by its username.
     *
     * @param  string $username
     *
     * @return \Symfony\Component\Security\Core\User\UserInterface|null The user or null if the user does not exist
     */
    public function findUserByUsername($username);

    /**
     * Finds one user by the given criteria.
     *
     * @param  array  $criteria
     *
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function findUserBy(array $criteria);

    /**
     * Bind the user on ldap.
     *
     * @param  UserInterface $user
     * @param  string        $password
     *
     * @return Boolean
     */
    public function bind(UserInterface $user, $password);
}

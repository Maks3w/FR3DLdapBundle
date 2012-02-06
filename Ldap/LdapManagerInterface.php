<?php

namespace FR3D\LdapBundle\Ldap;

use Symfony\Component\Security\Core\User\UserInterface;

interface LdapManagerInterface
{

    /**
     * Find a user by its username.
     *
     * @param string  $username
     * @return UserInterface or null if user does not exist
     */
    function findUserByUsername($username);

    /**
     * Finds one user by the given criteria.
     *
     * @param array $criteria
     * @return UserInterface
     */
    function findUserBy(array $criteria);

    /**
     * Bind the user on ldap
     *
     * @param UserInterface $user
     * @param string password
     * @return Boolean
     */
    function bind(UserInterface $user, $password);
}

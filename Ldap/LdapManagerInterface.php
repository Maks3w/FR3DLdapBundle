<?php

namespace FR3D\LdapBundle\Ldap;

use FR3D\LdapBundle\Model\LdapUserInterface;

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
     * @param LdapUserInterface $user
     * @param string password
     * @return Boolean
     */
    function bind(LdapUserInterface $user, $password);
}

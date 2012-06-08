<?php

namespace FR3D\LdapBundle\Model;

use FR3D\LdapBundle\Model\LdapUserInterface;

interface UserManagerInterface
{

    /**
     * Creates an empty user instance.
     *
     * @return LdapUserInterface
     */
    public function createUser();

    /**
     * Find a user by his username.
     *
     * @param string $username
     *
     * @return LdapUserInterface|null
     */
    public function findUserByUsername($username);
}

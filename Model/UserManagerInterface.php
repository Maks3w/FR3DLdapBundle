<?php

namespace FR3D\LdapBundle\Model;

use FR3D\LdapBundle\Model\LdapUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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

    /**
     * Updates a user.
     *
     * @param UserInterface $user
     *
     * @return void
     */
    public function updateUser(UserInterface $user);
}

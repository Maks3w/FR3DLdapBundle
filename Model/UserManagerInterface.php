<?php

namespace FR3D\LdapBundle\Model;

interface UserManagerInterface
{
    /**
     * Creates an empty user instance.
     *
     * @return LdapUserInterface
     */
    public function createUser();
}

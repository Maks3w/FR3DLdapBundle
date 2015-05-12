<?php

namespace FR3D\LdapBundle\Model;

/**
 * Simple User Manager implementation.
 */
class LdapUserManager implements UserManagerInterface
{
    protected $params = array();

    /**
     * Constructor.
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Returns an empty user instance.
     *
     * @return UserInterface
     */
    public function createUser()
    {
        $class = $this->params['user_class'];
        $user = new $class();

        return $user;
    }
}

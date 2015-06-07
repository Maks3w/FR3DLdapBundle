<?php

namespace FR3D\LdapBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Simple user object.
 */
class LdapUser implements LdapUserInterface, UserInterface
{
    use UserRoleTrait;

    const ROLE_DEFAULT = 'ROLE_USER';

    /**
     * @var string
     */
    protected $dn;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var array
     */
    protected $roles;

    public function __construct()
    {
        $this->roles = [];
    }

    /**
     * {@inheritDoc}
     */
    public function getDn()
    {
        return $this->dn;
    }

    /**
     * {@inheritDoc}
     */
    public function setDn($dn)
    {
        $this->dn = $dn;
    }

    /**
     * Removes sensitive data from the user.
     */
    public function eraseCredentials()
    {
        //Password not saved
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getSalt()
    {
        return null;
    }

    /**
     * Gets the encrypted password normally.
     * Not available via ldap.
     *
     * @return string
     */
    public function getPassword()
    {
        return '';
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function __toString()
    {
        return (string) $this->getUsername();
    }
}

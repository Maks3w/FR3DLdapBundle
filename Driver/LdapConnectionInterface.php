<?php

namespace FR3D\LdapBundle\Driver;

/**
 * Connection interface.
 * Driver connections must implement this interface.
 *
 */
interface LdapConnectionInterface
{

    /**
     * Bind to LDAP directory
     */
    function bind($user_dn, $password);

    function search($baseDn, $filter, array $attributes = array());
}

<?php

namespace FR3D\LdapBundle\Driver;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Driver interface.
 * Ldap drivers must implement this interface.
 *
 * @see http://www.php.net/ref.ldap.php
 */
interface LdapDriverInterface
{
    /**
     * Bind to LDAP directory.
     *
     * @param UserInterface $user     The user for authenticating the bind.
     * @param string        $password The password for authenticating the bind.
     *
     * @return bool true on success or false on failure
     *
     * @throws LdapDriverException if some error occurs.
     */
    public function bind(UserInterface $user, $password);

    /**
     * Search LDAP tree.
     *
     * @param  string        $baseDn     The base DN for the directory.
     * @param  string        $filter     The search filter.
     * @param  array         $attributes The array of the required attributes,
     *                                   'dn' is always returned. If array is
     *                                   empty then will return all attributes
     *                                   and their associated values.
     *
     * @return array|bool Returns a complete result information in a
     *                       multi-dimensional array on success and FALSE on error.
     *                       see {@link http://www.php.net/function.ldap-get-entries.php}
     *                       for array format examples.
     *
     * @throws LdapDriverException if some error occurs.
     */
    public function search($baseDn, $filter, array $attributes = []);
}

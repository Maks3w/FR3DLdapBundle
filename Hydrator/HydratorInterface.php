<?php

namespace FR3D\LdapBundle\Hydrator;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Defines methods for hydrate users.
 */
interface HydratorInterface
{
    /**
     * Populate an user with the data retrieved from LDAP.
     *
     * @param array $ldapEntry LDAP result information as a multi-dimensional array.
     *                         see {@link http://www.php.net/function.ldap-get-entries.php} for array format examples.
     */
    public function hydrate(array $ldapEntry): UserInterface;
}

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
     * @param array $ldapUserAttributes
     *
     * @return UserInterface
     */
    public function hydrate(array $ldapUserAttributes);

    /**
     * Add roles based on role configuration from ldap search.
     *
     * @param UserInterface
     */
    public function addRolesFromLdapGroup(array $ldapGroups, $nameAttribute);
}

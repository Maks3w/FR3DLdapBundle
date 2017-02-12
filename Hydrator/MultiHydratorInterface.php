<?php
/**
 * Created by Wassa. http://www.wassa.io
 * Date: 13/02/2017
 * Time: 01:14
 */

namespace FR3D\LdapBundle\Hydrator;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Defines methods to hydrate multiple users.
 */
interface MultiHydratorInterface
{
    /**
     * Populate users with the data retrieved from LDAP.
     *
     * @param array $ldapEntries LDAP result information as a multi-dimensional array.
     *              see {@link http://www.php.net/function.ldap-get-entries.php} for array format examples.
     *
     * @return array $users Newly created or updated users
     */
    public function hydrateAll(array $ldapEntries);
}

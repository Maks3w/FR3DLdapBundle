<?php

namespace FR3D\LdapBundle\Model;

use FR3D\LdapBundle\Ldap\Converter;

/**
 * UserRoleTrait provides methodes for roles.
 */
trait UserRoleTrait
{
    /**
     * Add roles based on role configuration from user memberOf attribute.
     *
     * @param array $memberOf
     * @param string $dnSuffixFilter
     */
    public function addRolesFromMemberof(array $memberOf, $dnSuffixFilter)
    {
        foreach ($memberOf as $role) {
            if (preg_match("/^cn=(.*),$dnSuffixFilter/", $role, $roleName)) {
                $this->addRole(sprintf('ROLE_%s',
                    Converter::strToSymRoleSchema($roleName[1])
                ));
            }
        }
    }
}

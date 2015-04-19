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

    /**
     * Add roles based on role configuration from ldap search.
     *
     * @param UserInterface
     */
    public function addRolesFromLdapGroup(array $ldapGroups, $nameAttribute)
    {
        foreach ($ldapGroups as $role) {
            if (isset($role[$nameAttribute])) {
                $this->addRole(sprintf('ROLE_%s',
                    Converter::strToSymRoleSchema($role[$nameAttribute])
                ));
            }
        }
    }

    /**
     * Adds a role to the user.
     *
     * @param string $role
     *
     * @return self
     */
    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * Returns the user roles.
     *
     * @return array The roles
     */
    public function getRoles()
    {
        $roles = $this->roles;

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }

    /**
     * Sets the roles of the user.
     * This overwrites any previous roles.
     *
     * @param array $roles
     *
     * @return self
     */
    public function setRoles(array $roles)
    {
        $this->roles = array();

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the SecurityContext, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $securityContext->isGranted('ROLE_USER');
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * Removes a role to the user.
     *
     * @param string $role
     *
     * @return self
     */
    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }
}

<?php

namespace FR3D\LdapBundle\Model;

/**
 * UserRoleTrait provides methods for roles.
 *
 * @property array $roles
 */
trait UserRoleTrait
{
    /**
     * Adds a role to the user.
     *
     * @param string $role
     *
     * @return
     */
    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return;
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
        if (empty($roles)) {
            $roles[] = static::ROLE_DEFAULT;
        }

        return array_unique($roles);
    }

    /**
     * Sets the roles of the user.
     * This overwrites any previous roles.
     *
     * @param array $roles
     *
     * @return
     */
    public function setRoles(array $roles)
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return;
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
     * @return
     */
    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return;
    }
}

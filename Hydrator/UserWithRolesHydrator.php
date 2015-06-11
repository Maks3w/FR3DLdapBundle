<?php

namespace FR3D\LdapBundle\Hydrator;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Simple hydrator implementation.
 */
class UserWithRolesHydrator extends AbstractHydrator
{
    protected $params;

    public function __construct(array $params, array $attributeMap)
    {
        parent::__construct($attributeMap);

        $this->params = $params;
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate(array $ldapUserAttributes)
    {
        $user = parent::hydrate($ldapUserAttributes);

        if (isset($this->params['role']) && isset($this->params['role']['search'])) {
            $this->addRolesFromLdapGroup($user, $ldapUserAttributes, $this->params['role']['search']['groupNameAttribute']);
        } elseif (isset($this->params['role']) && $this->params['role']['memberOf']) {
            $this->addRolesFromMemberOf($user, $ldapUserAttributes, $this->params['role']['memberOf']['dnSuffixFilter']);
        }

        return $user;
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

    /**
     * Add roles based on role configuration from user memberOf attribute.
     *
     * @param UserInterface $user
     * @param array $ldapUserAttributes
     * @param string $dnSuffixFilter
     */
    private function addRolesFromMemberOf(UserInterface $user, array $ldapUserAttributes, $dnSuffixFilter)
    {
        foreach ($ldapUserAttributes['memberof'] as $group) {
            if (preg_match("/^cn=(.*),$dnSuffixFilter/", $group, $roleName)) {
                $user->addRole(sprintf('ROLE_%s', self::strToSymRoleSchema($roleName[1])));
            }
        }
    }

    /**
     * Add roles based on role configuration from ldap search.
     *
     * @param UserInterface $user
     * @param array $ldapUserAttributes
     * @param string $groupNameAttribute
     */
    private function addRolesFromLdapGroup(UserInterface $user, array $ldapUserAttributes, $groupNameAttribute)
    {
        foreach ($ldapUserAttributes['groups'] as $group) {
            if (isset($group[$groupNameAttribute])) {
                $user->addRole(sprintf('ROLE_%s', self::strToSymRoleSchema($group[$groupNameAttribute][0])));
            }
        }
    }

    /**
     * Convert string to symfony role schema.
     *
     * @param string $role
     *
     * @return string
     */
    private static function strToSymRoleSchema($role)
    {
        $role = preg_replace('/\W+/', '_', $role);
        $role = trim($role, '_');
        $role = strtoupper($role);

        return $role;
    }
}

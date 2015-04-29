<?php

namespace FR3D\LdapBundle\Hydrator;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Offer methods for dynamic hydration based in attribute-method maps.
 */
trait HydrateWithMapTrait
{
    /**
     * Fill the given user with the following the attribute-method map.
     *
     * @param UserInterface $user Target user.
     * @param array[] $ldapUserAttributes Raw LDAP data.
     * @param string[] $attributeMap Attribute-method map.
     */
    protected function hydrateUserWithAttributesMap(
        UserInterface $user,
        array $ldapUserAttributes,
        array $attributeMap
    ) {
        foreach ($attributeMap as $attr) {
            if (!array_key_exists($attr['ldap_attr'], $ldapUserAttributes)) {
                continue;
            }

            $ldapValue = $ldapUserAttributes[$attr['ldap_attr']];

            if (!array_key_exists('count', $ldapValue) || $ldapValue['count'] == 1) {
                $value = $ldapValue[0];
            } else {
                $value = array_slice($ldapValue, 1);
            }

            call_user_func(array($user, $attr['user_method']), $value);
        }

        if (isset($this->params['role']['memberOf']) && isset($entry['memberof'])) {
            $this->addRolesFromMemberof($entry['memberof'], $this->params['role']['memberOf']['dnSuffixFilter']);
        }
    }

    /**
     * Add roles based on role configuration from user memberOf attribute.
     *
     * @param array $memberOf
     * @param string $dnSuffixFilter
     */
    public function addRolesFromMemberOf(array $memberOf, $dnSuffixFilter)
    {
        foreach ($memberOf as $role) {
            if (preg_match("/^cn=(.*),$dnSuffixFilter/", $role, $roleName)) {
                $this->addRole(sprintf('ROLE_%s', self::strToSymRoleSchema($roleName[1])));
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
                $this->addRole(sprintf('ROLE_%s', self::strToSymRoleSchema($role[$nameAttribute])));
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
    public static function strToSymRoleSchema($role)
    {
        $role = preg_replace('/\W+/', '_', $role);
        $role = trim($role, '_');
        $role = strtoupper($role);

        return $role;
    }
}

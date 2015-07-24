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

            if ((!array_key_exists('count', $ldapValue) && count($ldapValue) == 1) || (array_key_exists('count', $ldapValue) && $ldapValue['count'] == 1)) {
                $value = $ldapValue[0];
            } else {
                $value = array_slice($ldapValue, 1);
            }

            call_user_func([$user, $attr['user_method']], $value);
        }
    }
}

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

            if (array_key_exists('count', $ldapValue)) {
                unset($ldapValue['count']);
            }

            if (count($ldapValue) === 1) {
                $value = array_shift($ldapValue);
            } else {
                $value = $ldapValue;
            }

            call_user_func([$user, $attr['user_method']], $value);
        }
    }
}

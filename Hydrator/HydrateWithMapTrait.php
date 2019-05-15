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
     * @param UserInterface $user               target user
     * @param array[]       $ldapUserAttributes raw LDAP data
     * @param string[]      $attributeMap       attribute-method map
     */
    protected function hydrateUserWithAttributesMap(
        UserInterface $user,
        array $ldapUserAttributes,
        array $attributeMap
    ): void {
        foreach ($attributeMap as $attr) {
            if (!array_key_exists($attr['ldap_attr'], $ldapUserAttributes)) {
                continue;
            }

            $ldapValue = $ldapUserAttributes[$attr['ldap_attr']];

            if (array_key_exists('count', $ldapValue)) {
                unset($ldapValue['count']);
            }

            if (1 === count($ldapValue)) {
                $value = array_shift($ldapValue);
            } else {
                $value = $ldapValue;
            }

            $user->{$attr['user_method']}($value);
        }
    }
}

<?php

namespace FR3D\LdapBundle\Hydrator;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Populate a FOSUserBundle user with data from LDAP.
 *
 * @deprecated 3.0.0
 */
class LegacyHydrator implements HydratorInterface
{
    private $userManager;

    /**
     * @var string[]
     */
    private $attributeMap;

    public function __construct($userManager, array $attributeMap)
    {
        $this->userManager = $userManager;
        $this->attributeMap = $attributeMap;
    }

    /**
     * Populate an user with the data retrieved from LDAP.
     *
     * @param array $ldapUserAttributes
     *
     * @return UserInterface
     */
    public function hydrate(array $ldapUserAttributes)
    {
        $user = $this->createUser();

        foreach ($this->attributeMap as $attr) {
            if (!array_key_exists($attr['ldap_attr'], $ldapUserAttributes)) {
                continue;
            }

            $ldapValue = $ldapUserAttributes[$attr['ldap_attr']];

            if (!array_key_exists('count', $ldapValue) ||  $ldapValue['count'] == 1) {
                $value = $ldapValue[0];
            } else {
                $value = array_slice($ldapValue, 1);
            }

            call_user_func(array($user, $attr['user_method']), $value);
        }

        if ($user instanceof LdapUserInterface) {
            $user->setDn($ldapUserAttributes['dn']);
        }

        return $user;
    }

    /**
     * Create an empty user.
     *
     * @return UserInterface
     */
    protected function createUser()
    {
        $user = $this->userManager->createUser();
        $user->setPassword('');

        if ($user instanceof AdvancedUserInterface) {
            $user->setEnabled(true);
        }

        return $user;
    }
}

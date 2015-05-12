<?php

namespace FR3D\LdapBundle\Hydrator;

use FR3D\LdapBundle\Model\LdapUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Provide a hydrator template for easy implementation.
 */
abstract class AbstractHydrator implements HydratorInterface
{
    use HydrateWithMapTrait;

    /**
     * @var string[]
     */
    private $attributeMap;

    public function __construct(array $attributeMap)
    {
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

        $this->hydrateUserWithAttributesMap($user, $ldapUserAttributes, $this->attributeMap);

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
    abstract protected function createUser();
}

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
        $this->attributeMap = $attributeMap['attributes'];
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate(array $ldapEntry)
    {
        $user = $this->createUser();

        $this->hydrateUserWithAttributesMap($user, $ldapEntry, $this->attributeMap);

        if ($user instanceof LdapUserInterface) {
            $user->setDn($ldapEntry['dn']);
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    abstract protected function createUser();
}

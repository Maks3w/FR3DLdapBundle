<?php

namespace FR3D\LdapBundle\Hydrator;

use FR3D\LdapBundle\Model\LdapUserInterface;

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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    abstract protected function createUser();
}

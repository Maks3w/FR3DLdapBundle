<?php

namespace FR3D\LdapBundle\Hydrator;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Simple hydrator implementation.
 */
class StaticHydrator extends AbstractHydrator
{
    protected $params;

    public function __construct(array $params, array $attributeMap)
    {
        parent::__construct($attributeMap);

        $this->params = $params;
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
}

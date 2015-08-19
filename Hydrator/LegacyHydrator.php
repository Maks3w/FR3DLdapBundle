<?php

namespace FR3D\LdapBundle\Hydrator;

use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * Populate a FOSUserBundle user with data from LDAP.
 *
 * @deprecated 3.0.0
 */
final class LegacyHydrator extends AbstractHydrator
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @param array $attributeMap
     * @param UserManagerInterface $userManager
     */
    public function __construct(array $attributeMap, $userManager)
    {
        parent::__construct($attributeMap);

        $this->userManager = $userManager;
    }

    /**
     * {@inheritDoc}
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

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
     * @param UserManagerInterface $userManager
     * @param array $attributeMap
     */
    public function __construct($userManager, array $attributeMap)
    {
        parent::__construct($attributeMap);

        $this->userManager = $userManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function createUser()
    {
        if (!$this->ldapEntry) {
            throw new \Exception("Cannot hydrate user since we do not have LDAP entry data");
        }
        $user = $this->userManager->findUserBy(array('dn' => $this->ldapEntry['dn']));

        if (!$user) {
            $user = $this->userManager->createUser();
            $user->setPassword('');
            if ($user instanceof AdvancedUserInterface) {
                $user->setEnabled(true);
            }
        }

        return $user;
    }
}

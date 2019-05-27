<?php
/**
 * Created by Wassa. http://www.wassa.io
 * Date: 13/02/2017
 * Time: 01:14
 */

namespace FR3D\LdapBundle\Hydrator;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * Populate FOSUserBundle users with data from LDAP.
 *
 */
class MultiHydrator implements HydratorInterface, MultiHydratorInterface
{
    use HydrateWithMapTrait;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var string[]
     */
    private $attributeMap;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @param UserManagerInterface $userManager
     * @param array $attributeMap
     */
    public function __construct($userManager, array $attributeMap, array $params)
    {
        $this->userManager = $userManager;
        $this->attributeMap = $attributeMap['attributes'];
        $this->params = $params;
    }

    /**
     * {@inheritdoc}
     */
    public function hydrateAll(array $ldapEntries)
    {
        $users = $this->userManager->findUsers();
        $usersDone = [];

        for ($i = 0; $i < $ldapEntries['count']; $i++) {
            $ldapEntry = $ldapEntries[$i];
            $userFound = null;

            foreach ($users as $user) {
                if ($ldapEntry['uid'][0] == $user->getUsername()) {
                    $usersDone[] = $user;
                    $userFound = $user;
                    break;
                }
            }

            $user = $this->hydrate($ldapEntry, $userFound);
            $this->userManager->updateUser($user);
        }

        $usersNotDone = array_diff($users, $usersDone);

        foreach ($usersNotDone as $user) {
            if ($this->params['missing_accounts'] == 'delete') {
                $this->userManager->deleteUser($user);
            } elseif ($this->params['missing_accounts'] == 'disable') {
                if ($user instanceof AdvancedUserInterface) {
                    $user->setEnabled(false);
                    $this->userManager->updateUser($user);
                }
            }
        }

        return $usersDone;
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(array $ldapEntry, $user = null)
    {
        if (!$user) {
            $user = $this->createUser();
        }

        $this->hydrateUserWithAttributesMap($user, $ldapEntry, $this->attributeMap);

        if ($user instanceof LdapUserInterface) {
            $user->setDn($ldapEntry['dn']);
        }

        return $user;
    }

    /**
     * Create and returns a new FOSUserBundle User
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
FosGroups Ldap Lookup
=====================

The Roles Ldap Lookup methods can be adapted for FosUserBundle Groups to implement a more complicated permission system.

This example should give an idea how this could be implemented but without warranty or support.

### Configure FosUserBundle to use Groups
Documentation can be found in FOSUserBundle/Resources/doc/groups.md

### Customize hydrateRoles process
-Follow service creation documentation in [Override Ldap Manager](cookbook/override_ldap-manager.md)

**Extend LdapManager and customize him**

```` php
// src/Acme/DemoBundle/Ldap/LdapManager.php
<?php

namespace Acme\DemoBundle\Ldap;

use FR3D\LdapBundle\Ldap\LdapManager as BaseLdapManager;
use Symfony\Component\Security\Core\User\UserInterface;

class LdapManager extends BaseLdapManager
{
    protected function hydrateRoles(UserInterface $user, array $entry)
    {
        if (isset($this->params['role']['memberOf']) && isset($entry['memberof'])) {
            $groups = $this->groupManager->findGroupsByLdapAttribute($entry['memberof']);
            $user->setGroups($groups);
        }

        if (isset($this->params['role']['search'])) {
            $groupNames = array();
            $ldapGroups = $this->getLdapGroupsForUser($user);
            foreach ($ldapGroups as $ldapGroup) {
                if (isset($ldapGroup[$this->params['role']['search']['nameAttribute']])) {
                    $groupNames[] = $ldapGroup[$this->params['role']['search']['nameAttribute']];
                }
            }

            $groups = $this->groupManager->findGroupsByLdapAttribute($ldapGroups);
            $user->setGroups($groups);
        }

    }
}
````

### Customize FosUserBundle and Group Entity

**Extend FosUserBundle GroupManager and customize him**

```` php
// src/Acme/DemoBundle/Group/GroupManager.php
<?php

namespace Acme\DemoBundle\Group;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Doctrine\GroupManager as BaseGroupManager;

class GroupManager extends BaseGroupManager
{
    public function __construct(ObjectManager $om, $class, $dnSuffixFilter)
    {
        parent::__construct($om, $class);

        $this->objectManager = $om;
        $this->repository = $om->getRepository($class);

        $metadata = $om->getClassMetadata($class);
        $this->class = $metadata->getName();

        $this->dnSuffixFilter = $dnSuffixFilter;
    }

    public function findGroupsByLdapAttribute(array $groups)
    {
        if(!$this->dnSuffixFilter) {
            return false;
        }

        foreach ($groups as $key => &$groupName) {
            if (!strpos($groupName, $this->dnSuffixFilter)) {
                unset($groups[$key]);
                continue;
            }

            $groupName = strtoupper(preg_replace('/^.*cn=([0-9a-zA-Z-_ ]*),.*$/', '\1', $groupName));
        }

        return $this->repository->findGroupsBy($groups);
    }
}
````

**Add method to Doctrine Group Repository**
```` php
public function findGroupsBy(array $criteria, array $orderBy = null)
{
    $qb = $this->createQueryBuilder('g');
    $qb->add('select', 'g')
       ->add('from', 'AcmeDemoBundle:Group g')
       ;

    $count = 1;
    foreach ($criteria as $value) {
        if (null !== $value) {
            $filter = true;
            $qb->orWhere($qb->expr()->eq('g.name', "?$count"));
            $qb->setParameter($count, $value);
            $count++;
        }
    }

    return $qb->getQuery()->getResult();
}
````
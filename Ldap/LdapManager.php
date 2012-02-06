<?php

namespace FR3D\LdapBundle\Ldap;

use FR3D\LdapBundle\Driver\LdapConnectionInterface;
use FR3D\LdapBundle\Model\LdapUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

class LdapManager implements LdapManagerInterface
{
    private $connection;
    private $userManager;
    private $params = array();
    private $ldapAttributes = array();
    private $ldapUsernameAttr;

    public function __construct(LdapConnectionInterface $connection, $userManager, array $params)
    {
        $this->connection = $connection;
        $this->userManager = $userManager;
        $this->params = $params;

        foreach ($this->params['attributes'] as $attr) {
            $this->ldapAttributes[] = $attr['ldap_attr'];
        }

        $this->ldapUsernameAttr = $this->ldapAttributes[0];
    }

    /**
     * {@inheritDoc}
     */
    public function findUserByUsername($username)
    {
        return $this->findUserBy(array($this->ldapUsernameAttr => $username));
    }

    /**
     * {@inheritDoc}
     */
    public function findUserBy(array $criteria)
    {
        $filter  = $this->buildFilter($criteria);
        $entries = $this->connection->search($this->params['baseDn'], $filter, $this->ldapAttributes);
        if ($entries['count'] > 1) {
            throw new \Exception('This search can only return a single user');
        }

        if ($entries['count'] == 0) {
            return false;
        }
        $user = $this->userManager->createUser();
        $this->hydrate($user, $entries[0]);

        return $user;
    }

    /**
     * Build Ldap filter
     * 
     * @param array $criteria
     * 
     * @return string
     */
    private function buildFilter(array $criteria, $condition = '&')
    {
        $filters = array();
        $filters[] = $this->params['filter'];
        foreach ($criteria as $key => $value) {
            $filters[] = sprintf('(%s=%s)', $key, $value);
        }

        return sprintf('(%s%s)', $condition, implode($filters));
    }

    /**
     * Hydrates an user entity with ldap attributes.
     * 
     * @param UserInterface $user  user to hydrate
     * @param array             $entry ldap result
     * 
     * @return UserInterface
     */
    protected function hydrate(UserInterface $user, array $entry)
    {
        $user->setPassword('');

        if ($user instanceof AdvancedUserInterface) {
            $user->setEnabled(true);
        }

        foreach ($this->params['attributes'] as $attr) {
            call_user_func(array($user, $attr['user_method']), $entry[$attr['ldap_attr']][0]);
        }

        if ($user instanceof LdapUserInterface) {
            $user->setDn($entry['dn']);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function bind(UserInterface $user, $password)
    {
        return $this->connection->bind($user, $password);
    }

    /**
     * Get a list of roles for the username.
     *
     * @param string $username
     * @return array
     */
    public function getRolesForUsername($username)
    {
        
    }
}

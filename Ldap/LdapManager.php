<?php

namespace FR3D\LdapBundle\Ldap;

use FR3D\LdapBundle\Driver\LdapDriverInterface;
use FR3D\LdapBundle\Hydrator\HydratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class LdapManager implements LdapManagerInterface
{
    protected $driver;
    protected $params = [];

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    public function __construct(LdapDriverInterface $driver, HydratorInterface $hydrator, array $params)
    {
        $this->driver = $driver;
        $this->params = $params;
        $this->hydrator = $hydrator;
    }

    /**
     * {@inheritDoc}
     */
    public function findUserByUsername($username)
    {
        return $this->findUserBy([$this->params['usernameAttribute'] => $username]);
    }

    /**
     * {@inheritDoc}
     */
    public function findUserBy(array $criteria)
    {
        $filter = $this->buildFilter($criteria);
        $entries = $this->driver->search($this->params['baseDn'], $filter);
        if ($entries['count'] > 1) {
            throw new \Exception('This search can only return a single user');
        }

        if ($entries['count'] == 0) {
            return null;
        }

        if (isset($this->params['group']['baseDn'])) {
            $entries[0]['groups'] = $this->getLdapGroupsForUser($entries[0]);
        }

        $user = $this->hydrator->hydrate($entries[0]);

        return $user;
    }

    /**
     * Build Ldap filter.
     *
     * @param  array  $criteria
     * @param  string $condition
     *
     * @return string
     */
    protected function buildFilter(array $criteria, $condition = '&')
    {
        $filters = [];
        $filters[] = $this->params['filter'];
        $filter = new Filter\FilterValue();
        foreach ($criteria as $key => $value) {
            $value = $filter->filter($value);
            $filters[] = sprintf('(%s=%s)', $key, $value);
        }

        return sprintf('(%s%s)', $condition, implode($filters));
    }

    /**
     * {@inheritDoc}
     */
    public function bind(UserInterface $user, $password)
    {
        return $this->driver->bind($user, $password);
    }

    /**
     * Get a list of LdapGroups for the user.
     *
     * @param array $userEntries
     *
     * @return array
     */
    public function getLdapGroupsForUser(array $userEntries)
    {
        $userValueAttribute = $this->params['group']['userValueAttribute'];

        if (!array_key_exists($userValueAttribute, $userEntries)) {
            throw new \Exception('UserAttribute unknown.');
        }

        $filter = isset($this->params['group']['filter']) ? $this->params['group']['filter'] : '';
        $entries = $this->driver->search(
            $this->params['group']['baseDn'],
            sprintf('(&%s(%s=%s))', $filter, $this->params['group']['groupUserAttribute'], $userEntries[$userValueAttribute])
        );

        return $entries;
    }
}

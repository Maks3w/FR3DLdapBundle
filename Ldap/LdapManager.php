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
     * {@inheritdoc}
     */
    public function findUserByUsername($username)
    {
        return $this->findUserBy([$this->params['usernameAttribute'] => $username]);
    }

    /**
     * {@inheritdoc}
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
        foreach ($criteria as $key => $value) {
            $value = ldap_escape($value, '', LDAP_ESCAPE_FILTER);
            $filters[] = sprintf('(%s=%s)', $key, $value);
        }

        return sprintf('(%s%s)', $condition, implode($filters));
    }

    /**
     * {@inheritdoc}
     */
    public function bind(UserInterface $user, $password)
    {
        return $this->driver->bind($user, $password);
    }
}

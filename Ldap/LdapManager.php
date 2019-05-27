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

    public function findUserByUsername(string $username): ?UserInterface
    {
        return $this->findUserBy([$this->params['usernameAttribute'] => $username]);
    }

    public function findUserBy(array $criteria): ?UserInterface
    {
        $filter = $this->buildFilter($criteria);
        $entries = $this->driver->search($this->params['baseDn'], $filter);
        if ($entries['count'] > 1) {
            throw new \Exception('This search can only return a single user');
        }

        if (0 === $entries['count']) {
            return null;
        }
        $user = $this->hydrator->hydrate($entries[0]);

        return $user;
    }

    /**
     * Build Ldap filter.
     */
    protected function buildFilter(array $criteria, string $condition = '&'): string
    {
        $filters = [];
        $filters[] = $this->params['filter'];
        foreach ($criteria as $key => $value) {
            if ($value !== '*') {
                $value = ldap_escape($value, '', LDAP_ESCAPE_FILTER);
            }

            $filters[] = sprintf('(%s=%s)', $key, $value);
        }

        return sprintf('(%s%s)', $condition, implode($filters));
    }

    public function bind(UserInterface $user, string $password): bool
    {
        return $this->driver->bind($user, $password);
    }
    
    public function findAllUsers()
    {
        $filter = $this->buildFilter([$this->params['usernameAttribute'] => '*']);
        $entries = $this->driver->search($this->params['baseDn'], $filter);
        $users = [];
        
        if ($entries['count'] == 0) {
            return null;
        }

        $users = $this->hydrator->hydrateAll($entries);

        return $users;
    }
}

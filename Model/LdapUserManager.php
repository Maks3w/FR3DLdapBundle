<?php

namespace FR3D\LdapBundle\Model;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;

/**
 * Simple User Manager implementation.
 */
class LdapUserManager implements UserManagerInterface
{
    protected $params = array();

    /**
     * Constructor.
     */
    public function __construct(array $params)
    {
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

    /**
     * Refreshed a user by User Instance.
     *
     * Throws UnsupportedUserException if a User Instance is given which is not
     * managed by this UserManager (so another Manager could try managing it)
     *
     * It is strongly discouraged to use this method manually as it bypasses
     * all ACL checks.
     *
     * @deprecated Use FOS\UserBundle\Security\UserProvider instead
     *
     * @param SecurityUserInterface $user
     *
     * @return UserInterface
     */
    public function refreshUser(SecurityUserInterface $user)
    {
        trigger_error('Using the UserManager as user provider is deprecated. Use FOS\UserBundle\Security\UserProvider instead.', E_USER_DEPRECATED);

        $class = $this->getClass();
        if (!$user instanceof $class) {
            throw new UnsupportedUserException('Account is not supported.');
        }
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Expected an instance of FOS\UserBundle\Model\User, but got "%s".', get_class($user)));
        }

        $refreshedUser = $this->findUserBy(array('id' => $user->getId()));
        if (null === $refreshedUser) {
            throw new UsernameNotFoundException(sprintf('User with ID "%d" could not be reloaded.', $user->getId()));
        }

        return $refreshedUser;
    }
}

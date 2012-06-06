<?php

namespace FR3D\LdapBundle\Security\User;

use FR3D\LdapBundle\Ldap\LdapManagerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

/**
 * Provides users from Ldap
 */
class LdapUserProvider implements UserProviderInterface
{
    protected $ldapManager;

    public function __construct(LdapManagerInterface $ldapManager, LoggerInterface $logger = null)
    {
        $this->ldapManager = $ldapManager;
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($username)
    {
        $user = $this->ldapManager->findUserByUsername($username);

        if (empty($user)) {
            $this->logInfo("User $username not found on ldap");
            throw new UsernameNotFoundException(sprintf('User "%s" not found', $username));
        } else {
            $this->logInfo("User $username found on ldap");
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return true;
    }

    /**
     * Log a message into the logger if this exists
     *
     * @param string $message
     */
    private function logInfo($message)
    {
        if (!$this->logger) {
            return;
        }

        $this->logger->info($message);
    }
}

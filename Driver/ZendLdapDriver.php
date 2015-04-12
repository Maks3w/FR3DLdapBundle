<?php

namespace FR3D\LdapBundle\Driver;

use FR3D\LdapBundle\Model\LdapUserInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Zend\Ldap\Exception\LdapException as ZendLdapException;
use Zend\Ldap\Ldap;

/**
 * This class adapt ldap calls to Zend Framework Ldap library functions.
 * Also prevent information disclosure catching Zend Ldap Exceptions and passing
 * them to the logger.
 *
 * @since v2.0.0
 */
class ZendLdapDriver implements LdapDriverInterface
{
    /**
     * @var Ldap
     */
    private $driver;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Ldap            $driver Initialized Zend::Ldap Object
     * @param LoggerInterface $logger Optional logger for write debug messages.
     */
    public function __construct(Ldap $driver, LoggerInterface $logger = null)
    {
        $this->driver = $driver;
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function search($baseDn, $filter, array $attributes = array())
    {
        $this->logDebug(sprintf('ldap_search(%s, %s, %s)', $baseDn, $filter, implode(',', $attributes)));

        try {
            $entries          = $this->driver->searchEntries($filter, $baseDn, Ldap::SEARCH_SCOPE_SUB, $attributes);
            // searchEntries don't return 'count' key as specified by php native
            // function ldap_get_entries()
            $entries['count'] = count($entries);
        } catch (ZendLdapException $exception) {
            $this->zendExceptionHandler($exception);

            throw new LdapDriverException('An error occur with the search operation.');
        }

        return $entries;
    }

    /**
     * {@inheritDoc}
     */
    public function bind(UserInterface $user, $password)
    {
        if ($user instanceof LdapUserInterface && $user->getDn()) {
            $bind_rdn = $user->getDn();
        } else {
            $bind_rdn = $user->getUsername();
        }

        try {
            $this->logDebug(sprintf('ldap_bind(%s, ****)', $bind_rdn));
            $bind = $this->driver->bind($bind_rdn, $password);

            return ($bind instanceof Ldap);
        } catch (ZendLdapException $exception) {
            $this->zendExceptionHandler($exception);
        }

        return false;
    }

    /**
     * Treat a Zend Ldap Exception.
     *
     * @param ZendLdapException $exception
     */
    protected function zendExceptionHandler(ZendLdapException $exception)
    {
        switch ($exception->getCode()) {
            // Error level codes
            case ZendLdapException::LDAP_SERVER_DOWN:
                if ($this->logger) {
                    $this->logger->err($exception->getMessage());
                }
                break;

            // Other level codes
            default:
                $this->logDebug($exception->getMessage());
                break;
        }
    }

    /**
     * Log debug messages if the logger is set.
     *
     * @param string $message
     */
    private function logDebug($message)
    {
        if ($this->logger) {
            $this->logger->debug($message);
        }
    }
}

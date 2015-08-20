<?php

namespace FR3D\LdapBundle\Driver;

use FR3D\LdapBundle\Model\LdapUserInterface;
use Psr\Log\LoggerInterface;
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
     * {@inheritdoc}
     */
    public function search($baseDn, $filter, array $attributes = [])
    {
        $this->logDebug('{action}({base_dn}, {filter}, {attributes})', [
            'action' => 'ldap_search',
            'base_dn' => $baseDn,
            'filter' => $filter,
            'attributes' => $attributes,
        ]);

        try {
            $entries = $this->driver->searchEntries($filter, $baseDn, Ldap::SEARCH_SCOPE_SUB, $attributes);
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
     * {@inheritdoc}
     */
    public function bind(UserInterface $user, $password)
    {
        if ($user instanceof LdapUserInterface && $user->getDn()) {
            $bind_rdn = $user->getDn();
        } else {
            $bind_rdn = $user->getUsername();
        }

        try {
            $this->logDebug('{action}({bind_rdn}, ****)', [
                'action' => 'ldap_bind',
                'bind_rdn' => $bind_rdn,
            ]);
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
                    $this->logger->error('{exception}', ['exception' => $exception]);
                }
                break;

            // Other level codes
            default:
                $this->logDebug('{exception}', ['exception' => $exception]);
                break;
        }
    }

    /**
     * Log debug messages if the logger is set.
     *
     * @param string $message
     * @param array $context
     */
    private function logDebug($message, array $context = [])
    {
        if ($this->logger) {
            $this->logger->debug($message, $context);
        }
    }
}

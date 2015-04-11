<?php

namespace FR3D\LdapBundle\Driver;

use FR3D\LdapBundle\Model\LdapUserInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Zend\Stdlib\ErrorHandler;

/**
 * This class use php-ldap native functions for manage an ldap directory.
 *
 * @deprecated v2.0.0 Deprecated in favour of ZendLdapDriver
 * @since v1.0.0
 */
final class LegacyLdapDriver implements LdapDriverInterface
{
    private $params = array();

    /**
     * @var int Ldap Protocol version
     */
    private $version;
    private $logger;
    private $ldap_res;

    public function __construct(array $params, $version = 3, LoggerInterface $logger = null)
    {
        $this->params = $params;
        $this->version = $version;
        $this->logger = $logger;
    }

    public function search($baseDn, $filter, array $attributes = array())
    {
        if (null === $this->ldap_res) {
            $this->connect();
        }

        $this->logDebug(sprintf('ldap_search(%s, %s, %s)', $baseDn, $filter, implode(',', $attributes)));
        $search = ldap_search($this->ldap_res, $baseDn, $filter, $attributes);

        if ($search) {
            $entries = ldap_get_entries($this->ldap_res, $search);
            if (is_array($entries)) {
                return $entries;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     *
     * @uses connect()
     *
     * @throws LdapDriverException
     */
    public function bind(UserInterface $user, $password)
    {
        if ($user instanceof LdapUserInterface && $user->getDn()) {
            $bind_rdn = $user->getDn();
        } elseif (isset($this->params['bindRequiresDn']) && $this->params['bindRequiresDn']) {
            if (!isset($this->params['baseDn']) || !isset($this->params['accountFilterFormat'])) {
                throw new LdapDriverException('Param baseDn and accountFilterFormat is required if bindRequiresDn is true');
            }

            $bind_rdn = $this->search($this->params['baseDn'], sprintf($this->params['accountFilterFormat'], $user->getUsername()));
            if (1 == $bind_rdn['count']) {
                $bind_rdn = $bind_rdn[0]['dn'];
            } else {
                return false;
            }
        } else {
            $bind_rdn = $user->getUsername();
        }

        if (null === $this->ldap_res) {
            $this->connect();
        }

        $this->logDebug(sprintf('ldap_bind(%s, ****)', $bind_rdn));

        ErrorHandler::start(E_WARNING);
        $bind = ldap_bind($this->ldap_res, $bind_rdn, $password);
        ErrorHandler::stop();

        return $bind;
    }

    private function connect()
    {
        $host = $this->params['host'];
        if (isset($this->params['useSsl']) && (boolean) $this->params['useSsl']) {
            $host = sprintf('ldaps://%s:%d', $host, $this->params['port']);
        }

        ErrorHandler::start();
        $ress = ldap_connect($host, $this->params['port']);
        ErrorHandler::stop();

        if (isset($this->params['networkTimeout'])) {
            ldap_set_option($ress, LDAP_OPT_NETWORK_TIMEOUT, $this->params['networkTimeout']);
        }

        if (isset($this->params['useStartTls']) && (boolean) $this->params['useStartTls']) {
            ldap_start_tls($ress);
        }

        ldap_set_option($ress, LDAP_OPT_PROTOCOL_VERSION, $this->version);

        if (isset($this->params['optReferrals']) && null !== $this->params['optReferrals']) {
            ldap_set_option($ress, LDAP_OPT_REFERRALS, $this->params['optReferrals']);
        }

        if (isset($this->params['username'])) {
            if (!isset($this->params['password'])) {
                throw new LdapDriverException('You must set a password in config');
            }

            ErrorHandler::start(E_WARNING);
            $bindress = ldap_bind($ress, $this->params['username'], $this->params['password']);
            ErrorHandler::stop();
        } else {
            ErrorHandler::start(E_WARNING);
            $bindress = ldap_bind($ress);
            ErrorHandler::stop();
        }

        if (!$bindress) {
            throw new LdapDriverException('Unable to connect Ldap');
        }

        $this->ldap_res = $ress;
    }

    private function logDebug($message)
    {
        if (!$this->logger) {
            return;
        }

        $this->logger->debug($message);
    }
}

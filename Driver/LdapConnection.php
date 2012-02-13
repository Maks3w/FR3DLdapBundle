<?php

namespace FR3D\LdapBundle\Driver;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;

class LdapConnection implements LdapConnectionInterface
{
    private $params = array();
    private $logger;
    private $ldap_res;

    public function __construct(array $params, LoggerInterface $logger = null)
    {
        $this->params   = $params;
        $this->logger   = $logger;
        $this->ldap_res = NULL;
    }

    public function search($baseDn, $filter, array $attributes = array())
    {
        if ($this->ldap_res == NULL) {
            $this->connect();
        }

        $this->logDebug("ldap_search($this->ldap_res, $baseDn, $filter, $attributes)");
        $search = ldap_search($this->ldap_res, $baseDn, $filter, $attributes);

        if ($search) {
            $entries = ldap_get_entries($this->ldap_res, $search);
            if (is_array($entries)) {
                return $entries;
            } else {
                return false;
            }
        }
    }

    public function bind($user_dn, $password)
    {
        if ($this->ldap_res == NULL) {
            $this->connect();
        }
        if (!$user_dn) {
            $this->logInfo('You must bind with an ldap user_dn');
            return false;
        }

        if (!$password) {
            $this->logInfo('Password can not be null to bind');
            return false;
        }

        return @ldap_bind($this->ldap_res, $user_dn, $password);
    }

    /**
     * Ping the LDAP server before trying to open the LDAP connection.
     * This compensates for the lack of a timeout parameter on the PHP LDAP library.
     * Without this, the LDAP library will listen for a long period of time,
     * resulting in the user having to wait far too long.
     *
     * Timeout is two seconds.
     *
     * @throws AuthenticationServiceException If the LDAP server is unavailable.
     */
    private function checkServer() {
        $socket = @fsockopen($this->params['host'], $this->params['port'], $errno, $errstr, 2);

            throw new AuthenticationServiceException(sprintf('LDAP Server Unavailable: Error %s: %s.', $errno, $errstr));
        }

        fclose($socket);
    }

    private function connect()
    {
        $host = $this->params['host'];

        // Check if the server is alive
        $this->checkServer();

        if (isset($this->params['useSsl']) && (boolean) $this->params['useSsl']) {
            $host = 'ldaps://' . $host;
        }

        $ress = @ldap_connect($host, $this->params['port']);

        if (isset($this->params['useStartTls']) && (boolean) $this->params['useStartTls']) {
            ldap_start_tls($ress);
        }

        if (isset($this->params['version']) && $this->params['version'] !== null) {
            ldap_set_option($ress, LDAP_OPT_PROTOCOL_VERSION, $this->params['version']);
        }

        if (isset($this->params['optReferrals']) && $this->params['optReferrals'] !== null) {
            ldap_set_option($ress, LDAP_OPT_REFERRALS, $this->params['optReferrals']);
        }

        if (isset($this->params['username']) && $this->params['version'] !== null) {
            if (!isset($this->params['password'])) {
                throw new \Exception('You must uncomment password key');
            }
            $bindress = @ldap_bind($ress, $this->params['username'], $this->params['password']);

            if (!$bindress) {
                throw new \Exception('The credentials you have configured are not valid');
            }
        } else {
            $bindress = @ldap_bind($ress);

            if (!$bindress) {
                throw new \Exception('Unable to connect Ldap');
            }
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

    private function logInfo($message)
    {
        if (!$this->logger) {
            return;
        }

        $this->logger->info($message);
    }
}

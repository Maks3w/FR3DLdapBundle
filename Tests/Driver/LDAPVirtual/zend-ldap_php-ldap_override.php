<?php

/*
 * This file override default PHP Ldap functions for the specified namespace.
 */

namespace Zend\Ldap;

/** @var \FR3D\LdapBundle\Tests\Driver\LDAPVirtual\LDAPVirtualInterface $ldapServer */
$ldapServer = null;

function ldap_bind($ress, $username = null, $password = null)
{
    global $ldapServer;

    if (!is_resource($ress)) {
        throw new \Exception('Invalid LDAP resource');
    }

    if (null == $username) {
        return true;
    }

    return $ldapServer->ldap_bind($username, $password);
}

function ldap_connect($host, $port = 389)
{
    global $ldapServer;

    $url = parse_url($host);

    if (isset($url['host'])) {
        if ($ldapServer->ldap_connect($url['host'], $url['port'])) {
            return \ldap_connect();
        }
    } else {
        if ($ldapServer->ldap_connect($host, $port)) {
            return \ldap_connect();
        }
    }

    return false;
}

function ldap_search($ress, $baseDn, $filter, array $attributes = array())
{
    global $ldapServer;

    if (!is_resource($ress)) {
        throw new \Exception('Invalid LDAP resource');
    }

    return $ldapServer->ldap_search($baseDn, $filter, $attributes);
}

function ldap_set_option($ress, $option, $value)
{
    global $ldapServer;

    if (!is_resource($ress)) {
        throw new \Exception('Invalid LDAP resource');
    }

    $return = $ldapServer->ldap_set_option($option, $value);

    // If ldap_set_option is not mocked we'll return true.
    return ($return) ? $return : true;
}

function ldap_start_tls($ress)
{
    global $ldapServer;

    if (!is_resource($ress)) {
        throw new \Exception('Invalid LDAP resource');
    }

    if (!$ldapServer->ldap_start_tls($ress)) {
        throw new \Exception('TLS not supported');
    }

    $return = $ldapServer->ldap_start_tls();

    // If ldap_set_option is not mocked we'll return true.
    return ($return) ? $return : true;
}

function ldap_get_entries($ress, $result)
{
    global $ldapServer;

    if (!is_resource($ress)) {
        throw new \Exception('Invalid LDAP resource');
    }

    return $ldapServer->ldap_get_entries($result);
}

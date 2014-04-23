<?php

namespace FR3D\LdapBundle\Tests\Driver\LDAPVirtual;

interface LDAPVirtualInterface
{

    function setTls($tls);

    function setSsl($ssl);

    function setOptions($options);

    function addUser($username, $password);

    function ldap_bind($username, $password);

    function ldap_connect($host, $port);

    function ldap_set_option($option, $value);

    function ldap_start_tls();

    function ldap_search($baseDn, $filter, $attributes);

    function ldap_get_entries();
}

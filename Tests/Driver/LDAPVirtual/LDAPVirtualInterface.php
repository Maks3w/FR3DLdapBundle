<?php

namespace FR3D\LdapBundle\Tests\Driver\LDAPVirtual;

interface LDAPVirtualInterface
{
    public function setTls($tls);

    public function setSsl($ssl);

    public function setOptions($options);

    public function addUser($username, $password);

    public function ldap_bind($username, $password);

    public function ldap_connect($host, $port);

    public function ldap_set_option($option, $value);

    public function ldap_start_tls();

    public function ldap_search($baseDn, $filter, $attributes);

    public function ldap_get_entries();
}

<?php

namespace FR3D\LdapBundle\Model;

interface LdapUserInterface
{
    /**
     * Set Ldap Distinguished Name
     *
     * @param string $dn Distinguished Name
     */
    function setDn($dn);

    /**
     * Get Ldap Distinguished Name
     *
     * @return string Distinguished Name
     */
    function getDn();
}

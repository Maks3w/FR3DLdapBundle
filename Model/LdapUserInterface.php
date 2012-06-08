<?php

namespace FR3D\LdapBundle\Model;

interface LdapUserInterface
{
    /**
     * Set Ldap Distinguished Name
     *
     * @param string $dn Distinguished Name
     */
    public function setDn($dn);

    /**
     * Get Ldap Distinguished Name
     *
     * @return string Distinguished Name
     */
    public function getDn();
}

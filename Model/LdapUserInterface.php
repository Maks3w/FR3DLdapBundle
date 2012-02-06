<?php

namespace FR3D\LdapBundle\Model;

interface LdapUserInterface
{
    /**
     * Set Ldap Distinguised Name
     *
     * @param string Distinguised Name
     */
    function setDn($dn);

    /**
     * Get Ldap Distinguised Name
     *
     * @return string Distinguised Name
     */
    function getDn();
}

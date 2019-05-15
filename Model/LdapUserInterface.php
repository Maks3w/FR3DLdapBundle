<?php

namespace FR3D\LdapBundle\Model;

interface LdapUserInterface
{
    /**
     * Set Ldap Distinguished Name.
     *
     * @param string $dn Distinguished Name
     */
    public function setDn(string $dn);

    /**
     * Get Ldap Distinguished Name.
     *
     * @return string|null Distinguished Name
     */
    public function getDn(): ?string;
}

<?php

namespace FR3D\LdapBundle\Tests;

use FR3D\LdapBundle\Model\LdapUserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TestUser implements UserInterface, AdvancedUserInterface, LdapUserInterface
{
    private $username;
    private $password;
    private $enabled;
    private $locked;
    private $dn;
    private $roles = [];

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function setLocked($locked)
    {
        $this->locked = $locked;
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    public function isAccountNonLocked()
    {
        return !$this->locked;
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function setDn(string $dn)
    {
        $this->dn = $dn;
    }

    public function getDn(): ?string
    {
        return $this->dn;
    }

    public function __toString()
    {
        return (string) $this->getUsername();
    }
}

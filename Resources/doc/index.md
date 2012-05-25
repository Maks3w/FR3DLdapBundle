LdapBundle
==========

LdapBundle provides a Ldap authentication system without the `apache mod_ldap`. He use `php-ldap` package with a form to authenticate the users. LdapBundle also can be used for the authorization. He retrieves the  Ldap users' roles.

This bundle is based on the original work of BorisMorel and adapted to use with FOSUserBundle

Install
-------
1. Add FR3DLdapBundle in your composer.json
2. Enable the Bundle
3. Configure security.yml
4. Configure config.yml
5. Enable FOSUserBundle as User Provider
6. Implement LdapUserInterface on your User Class

### 1. Add FR3DLdapBundle in your composer.json

Add this bundle to your ``vendor/`` dir:

```json
{
    "require": {
        "fr3d/ldap-bundle": "1.6.*"
    }
}
```

### 2. Enable the Bundle

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new FR3D\LdapBundle\FR3DLdapBundle(),
    );
}
```

### 3. Configure security.yml
``` yaml
# app/config/security.yml

security:
  firewalls:
    main:
      pattern:          ^/
      fr3d_ldap:        ~
      form_login:
          always_use_default_target_path: true
          default_target_path: /profile
      logout:       true
      anonymous:    true

  providers:
    fr3d_ldapbundle:
      id: fr3d_ldap.security.user.provider

  encoders:
      AcmeBundle\Acme\User\LdapUser: plaintext
```

### 4. Configure config.yml
``` yaml
# app/config/config.yml
fr3d_ldap:
    client:
        host:         your.host.foo
#       port:         389    # Optional
#       version:        3    # Optional
#       username:     foo    # Optional
#       password:     bar    # Optional
#       optReferrals: false  # Optional
#       useSsl:       true   # Enable SSL negotiation. Optional
#       useStartTls:  true   # Enable TLS negotiation. Optional
    user:
        baseDn: ou=users, dc=host, dc=foo
        filter: (&(ObjectClass=Person))
        attributes:          # Expecify ldap attributes mapping [ldap attribute, user object method]
#           - { ldap_attr: uid,  user_method: setUsername } # Default
#           - { ldap_attr: cn,   user_method: setName }     # Optional
#           - { ldap_attr: ...,  user_method: ... }         # Optional
#   service:
#       user_manager: fos_user.user_manager          # Overrides default user manager
#       ldap_manager: fr3d_ldap.ldap_manager.default # Overrides default ldap manager
```

**You need to configure the parameters under the fr3d_ldap section.**

### 5. Enable FOSUserBundle as User Provider

In security.yml make a chain_provider with fos_userbundle before fr3d_ldapbundle

``` yaml
# app/config/security.yml

security:
    providers:
        chain_provider:
            providers: [fos_userbundle, fr3d_ldapbundle]

        fr3d_ldapbundle:
            id: fr3d_ldap.security.user.provider

        fos_userbundle:
            id: fos_user.user_manager

```

### 6. Implement LdapUserInterface on your User Class

It's necesary implement `FR3D\LdapBundle\Model\LdapUserInterface` on your `User` for manipulate the ldap object Distinguished Name (DN)

OPTIONAL: You could persist $dn attribute for speedup authentication process.

````php
<?php
// src/Acme/UserBundle/Entity/User.php

namespace Acme\UserBundle\Entity;

use FR3D\LdapBundle\Model\LdapUserInterface;

class User implements LdapUserInterface
{
    /**
     * Ldap Object Distinguished Name
     * @var string $dn
     */
    private $dn;

    /**
     * {@inheritDoc}
     */
    public function setDn($dn)
    {
        $this->dn = $dn;
    }

    /**
     * {@inheritDoc}
     */
    public function getDn()
    {
        return $this->dn;
    }
}
````

### Cookbook

Look the cookbook for another interesting things.

- [Override Ldap Manager](cookbook/override_ldap-manager.md)
- [Prevent guess registration with usernames already exists on LDAP](cookbook/validator.md)

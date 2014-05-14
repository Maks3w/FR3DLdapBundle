LdapBundle
==========

LdapBundle provides a Ldap authentication system without Apache's `mod_ldap` module.
It uses the `php-ldap` package with a form to authenticate the users.
LdapBundle can also be used for the authorization process. It retrieves the Ldap users' roles.

This bundle is based on the original work of BorisMorel and adapted for use with FOSUserBundle

This bundle requires Zend Ldap v2.

Install
-------
1. Add FR3DLdapBundle in your composer.json
2. Enable the Bundle
3. Configure security.yml
4. Configure config.yml
5. Enable FOSUserBundle as User Provider

### 1. Add FR3DLdapBundle in your composer.json

Add this bundle to your `vendor/` dir:

```json
{
    "require": {
        "fr3d/ldap-bundle": "2.0.*@dev"
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
      pattern:    ^/
      fr3d_ldap:  ~
      form_login:
          always_use_default_target_path: true
          default_target_path: /profile
      logout:     true
      anonymous:  true

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
    driver:
        host:                your.host.foo
#       port:                389    # Optional
#       username:            foo    # Optional
#       password:            bar    # Optional
#       bindRequiresDn:      true   # Optional
#       baseDn:              ou=users, dc=host, dc=foo   # Optional
#       accountFilterFormat: (&(uid=%s)) # Optional. sprintf format %s will be the username
#       optReferrals:        false  # Optional
#       useSsl:              true   # Enable SSL negotiation. Optional
#       useStartTls:         true   # Enable TLS negotiation. Optional
    user:
        baseDn: ou=users, dc=host, dc=foo
        filter: (&(ObjectClass=Person))
        attributes:          # Specify ldap attributes mapping [ldap attribute, user object method]
#           - { ldap_attr: uid,  user_method: setUsername } # Default
#           - { ldap_attr: cn,   user_method: setName }     # Optional
#           - { ldap_attr: ...,  user_method: ... }         # Optional
#   service:
#       user_manager: fos_user.user_manager          # Overrides default user manager
#       ldap_manager: fr3d_ldap.ldap_manager.default # Overrides default ldap manager
```

**You need to configure the parameters under the fr3d_ldap section.**

### 5. Enable FOSUserBundle as User Provider

In security.yml make a `chain_provider` with `fos_userbundle` before `fr3d_ldapbundle` .

``` yaml
# app/config/security.yml

security:
    providers:
        chain_provider:
            chain:
                providers: [fos_userbundle, fr3d_ldapbundle]

        fr3d_ldapbundle:
            id: fr3d_ldap.security.user.provider

        fos_userbundle:
            id: fos_user.user_provider.username

```

### Cookbook

Look the cookbook for other interesting things.

- [Override Ldap Manager](cookbook/override_ldap-manager.md)
- [Prevent registration with a username that already exists on LDAP](cookbook/validator.md)
- [Example configuration for Active Directory](cookbook/active-directory.md)

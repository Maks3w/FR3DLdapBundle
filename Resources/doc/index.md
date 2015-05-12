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

```php
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
```yaml
# app/config/security.yml

security:
  # Preserve plain text password in token for refresh the user.
  # Analyze the security considerations before turn off this setting.
  erase_credentials: false

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

**Optional you can also enable basic authentication**
```yaml
# app/config/security.yml

security:
  firewalls:
    api:
      pattern:    ^/api
      fr3d_ldap_httpbasic: ~
```

### 4. Configure config.yml
```yaml
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
#       accountCanonicalForm: 3 # ACCTNAME_FORM_BACKSLASH this is only needed if your users have to login with something like HOST\User
#       accountDomainName: HOST
#       accountDomainNameShort: HOST # if you use the Backslash form set both to Hostname than the Username will be converted to HOST\User
    user:
        baseDn: ou=users, dc=host, dc=foo
        filter: (&(ObjectClass=Person))
#       usernameAttribute: uid # Optional
        attributes:          # Specify ldap attributes mapping [ldap attribute, user object method]
#           - { ldap_attr: uid,  user_method: setUsername } # Default
#           - { ldap_attr: cn,   user_method: setName }     # Optional

#           - { ldap_attr: ...,  user_method: ... }         # Optional
#   service:
#       user_hydrator: fr3d_ldap.user_hydrator.default # Overrides default user hydrator
#       ldap_manager: fr3d_ldap.ldap_manager.default   # Overrides default ldap manager
```

**You need to configure the parameters under the fr3d_ldap section.**

### 5. Enable FOSUserBundle as User Provider

In security.yml make a `chain_provider` with `fos_userbundle` before `fr3d_ldapbundle` .

```yaml
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

Make sure to set all needed fields when creating the user otherwise you will get an exception. This can easily be done in the config.yml

```yaml
# app/config/config.yml
fr3d_ldap:
    ...
    user:
      - { ldap_attr: uid,  user_method: setUsername }
      - { ldap_attr: mail, user_method: setEmail }
```

### Cookbook

Look the cookbook for other interesting things.

- [Create a custom hydrator](cookbook/custom_hydrator.md)
- [Prevent registration with a username that already exists on LDAP](cookbook/validator.md)
- [Example configuration for Active Directory](cookbook/active-directory.md)

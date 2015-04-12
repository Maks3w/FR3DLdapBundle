Standalone Installation
==========

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
        "noles/ldap-bundle": "2.0.*@dev"
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

**Optional you can also enable basic authentication**
``` yaml
# app/config/security.yml

security:
  firewalls:
    api:
      pattern:    ^/api
      fr3d_ldap_httpbasic: ~
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
#       accountCanonicalForm: 3 # ACCTNAME_FORM_BACKSLASH this is only needed if your users have to login with something like HOST\User
#       accountDomainName: HOST
#       accountDomainNameShort: HOST # if you use the Backslash form set both to Hostname than the Username will be converted to HOST\User
#    manager:
#        user_class: FR3D\LdapBundle\Model\LdapUser # Overrides default user class
    user:
        baseDn: ou=users, dc=host, dc=foo
        filter: (&(ObjectClass=Person))
        attributes:          # Specify ldap attributes mapping [ldap attribute, user object method]
#           - { ldap_attr: uid,  user_method: setUsername } # Default
#           - { ldap_attr: cn,   user_method: setName }     # Optional

#           - { ldap_attr: ...,  user_method: ... }         # Optional
#        role:
#            memberOf
#                dnSuffixFilter: ou=Roles,dc=example,dc=com
#            search:
#                baseDn: ou=Roles,dc=example,dc=com
#                nameAttribute: cn
#                userDnAttribute: member
#                userId: dn
#   service:
#       user_manager: fr3d_ldap.user_manager.default # Overrides default user manager
#       ldap_manager: fr3d_ldap.ldap_manager.default # Overrides default ldap manager
```

**You need to configure the parameters under the fr3d_ldap section.**
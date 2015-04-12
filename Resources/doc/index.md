LdapBundle
==========

LdapBundle provides a Ldap authentication system without Apache's `mod_ldap` module.
It uses the `php-ldap` package with a form to authenticate the users.
LdapBundle can also be used for the authorization process. It retrieves the Ldap users' roles.

This bundle is based on the original work of BorisMorel and adapted for use with FOSUserBundle by Maks3w.

FR3DLdapBundle requires Zend Ldap v2 and can be installed standalone or combined with FOSUserBundle.

Install
-------
1. [Standalone install](install/standalone.md)
2. Combined with FOSUserBundle(install/combined_with_fosuser.md)

### Cookbook

Look the cookbook for other interesting things.

- [Override Ldap Manager](cookbook/override_ldap-manager.md)
- [Prevent registration with a username that already exists on LDAP](cookbook/validator.md)
- [Example configuration for Active Directory](cookbook/active-directory.md)
- [Example configuration for Ldap Roles lookup](cookbook/roles_from_ldap.md)

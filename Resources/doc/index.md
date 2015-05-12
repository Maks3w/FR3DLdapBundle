LdapBundle
==========

LdapBundle provides a Ldap authentication system without Apache's `mod_ldap` module.
It uses the `php-ldap` package with a form to authenticate the users.
LdapBundle can also be used for the authorization process. It retrieves the Ldap users' roles.

This bundle is based on the original work of BorisMorel and adapted for use with FOSUserBundle by Maks3w.

FR3DLdapBundle requires Zend Ldap v2 and can be installed standalone or combined with FOSUserBundle.

Install
-------
* [Standalone install](install/standalone.md)
* [Combined with FOSUserBundle](install/combined_with_fosuser.md)

### Cookbook

Look the cookbook for other interesting things.

- [Create a custom hydrator](cookbook/custom_hydrator.md)
- [Prevent registration with a username that already exists on LDAP](cookbook/validator.md)
- [Example configuration for Active Directory](cookbook/active-directory.md)

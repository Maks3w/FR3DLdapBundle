Changelog
=========

### v2.0.0

Minimum Requirements:
- Symfony 2.1 or greater
- Zend LDAP 2.0 or greater

Ldap connection:
- Connection component has been renamed to Driver.

Ldap driver:
- [Feature]  Added support for Zend Framework Ldap library
- [Feature]  Now you could authenticate without DN if your LDAP support that.
- [BC Break] Default Ldap driver changed to Zend Ldap v2.
- Old Ldap driver declared as deprecated and will be removed in next releases.
- [BC Break] Renamed service `fr3d_ldap.client` to `fr3d_ldap.ldap_driver`
- Added parameter `fr3d_ldap.ldap_driver.protocol.version` for specify LDAP
  protocol version for who need that.

Ldap User:
- [Feature] It's not longer required implement LdapUserInterface on your User class.
  Anyway if your LDAP has bindRequiredDn = true you could still using the interface
  for speedup the authentication.

Config:
- [BC Break] Renamed root option `client` to `driver`
- [BC Break] Removed `version` key in config.yml
- [BC Break] bindRequiredDn it's false by default, in v1.5.0 works as true
- [Feature] Now you can use all Zend Ldap options by the same way described in
  http://framework.zend.com/manual/en/zend.ldap.api.html

### v1.5.2, v1.6.1  (2012-02-18)

* Add support for Composer package manager now you can find this bundle in http://www.packagist.org
* [Security] Sanitize user input.
* Other fixes in sync with Symfony updates.

### v1.6.0  (2012-02-17)

* Add support for Symfony master (Symfony 2.1)

### v1.5.1  (2012-02-06)

* [Security] Prevent accidental information disclosure

### v1.5.0  (2012-01-29)

* Enhancement release

Features:

- All code refactored
- Extensible and customized code
- Added unique usernames validator for third party bundles

### v1.0.0  (2012-01-03)

* Initial release

Features:

- User provider for LDAP based directories
- Authentication provider with for LDAP based directories
- Compatible with FOSUserBundle
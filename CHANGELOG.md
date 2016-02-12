Changelog
=========

### v3.0.0
Ldap driver:
- [BC Break] Remove support for PHP 5.3 and 5.4
- [BC Break] Remove legacy Ldap driver declared as deprecated in 2.0.0.
- [BC Break] `hydrate()` method in LdapManager has been moved to `Model\LegacyHydrator.php`.
  See [Create a custom hydrator](Resources/doc/cookbook/custom_hydrator.md) for more details.
- [BC Break] This bundle is now PSR-3 (Logger) compliant. Typehints has been updated in favor of `Psr\Log\LoggerInterface`.
- [BC Break] Remove `FR3D\LdapBundle\Ldap\Converter`
- [BC Break] Remove `escapeValue()` and `unescapeValue()` methods from `LdapManagerInterface` and `LdapManager`. Now use
  PHP 5.6 [ldap_escape()](http://php.net/manual/function.ldap-escape.php) polyfill.

Config:
- [BC Break] Added `fr3d_ldap.user.usernameAttribute` for indicate the attribute which holds the username.
  Previously was the first entry in `attributes`

Validator:
- Removed unused `property` option

### v2.0.0

Minimum Requirements:
- [Symfony](https://github.com/symfony/symfony) 2.3 or greater
- [Zend LDAP](https://github.com/zendframework/Component_ZendLdap) 2.0 or greater

Ldap connection:
- Connection component has been renamed to Driver.

Ldap driver:
- [Feature]  Added [HTTP Basic authentication listener](https://github.com/Maks3w/FR3DLdapBundle/blob/2.0.x/Resources/doc/index.md#3-configure-securityyml) by @Noles
- [Feature]  Added support for Zend Framework Ldap library
- [Feature]  Now you could authenticate without DN if your LDAP support that.
- [BC Break] Default Ldap driver changed to Zend Ldap v2.
- Old Ldap driver declared as deprecated and will be removed in next releases.
- [BC Break] Renamed service `fr3d_ldap.client` to `fr3d_ldap.ldap_driver`
- Added parameter `fr3d_ldap.ldap_driver.protocol.version` for specify LDAP
  protocol version for who need that.

Ldap User:
- [Feature] LdapUserInterface it's not longer required on your User class.
  Anyway if your LDAP has bindRequiredDn = true you could still using the interface
  for speedup the authentication.

Config:
- [Feature] Now you can use all Zend Ldap options in the same way described in
  http://framework.zend.com/manual/current/en/modules/zend.ldap.api.html
- [BC Break] Renamed root option `client` to `driver`
- [BC Break] Removed `version` key in config.yml
- [BC Break] `bindRequiredDn` it's false by default, in v1.5.0 works as true

Security:
- [BC Break] You may need set `erase_credentials` setting to `false` if you encounter problems when the user
 reauthenticate. See [issue#76](https://github.com/Maks3w/FR3DLdapBundle/issues/76) for more details.

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
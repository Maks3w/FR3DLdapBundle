FR3DLdapBundle
==============

FR3DLdapBundle provide users and authentication services based on LDAP directories for Symfony2 framework.

The bundle is mainly developed for unmanaged LDAP directories scenarios, so
you will able to retrieve users from LDAP and manage them using the [FOSUserBundle features](https://github.com/FriendsOfSymfony/FOSUserBundle/) (role management, guess users registration, etc) without the need to modify your corporate directory.


Features included:

- Works with with [FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle/)
- Customizable and extensible
- Unit tested

**Note:** This bundle cannot work as standalone at this moment and requires an User Manager like `FOSUserBundle`

**Versions and compatibilities:**
- 1.5.x is compatible with Symfony 2.0.x and is recommended for stable projects.
- 1.6.x is compatible with Symfony 2.1.x and has the same features than 1.5.x
- 2.0.x is actually compatible with Symfony 2.0.x and have new features. This version is actually under development and is subject to changes.

[![Build Status](https://secure.travis-ci.org/Maks3w/FR3DLdapBundle.png?branch=master)](http://travis-ci.org/Maks3w/FR3DLdapBundle)

Documentation
-------------

The bulk of the documentation is stored in the `Resources/doc/index.md`
file in this bundle:

[Read the Documentation](https://github.com/Maks3w/FR3DLdapBundle/blob/master/Resources/doc/index.md)

Installation
------------

All the installation instructions are located in [documentation](https://github.com/Maks3w/FR3DLdapBundle/blob/master/Resources/doc/index.md).

License
-------

This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE

Reporting an issue or a feature request
---------------------------------------

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/Maks3w/FR3DLdapBundle/issues).

When reporting a bug, it may be a good idea to reproduce it in a basic project
built using the [Symfony Standard Edition](https://github.com/symfony/symfony-standard)
to allow developers of the bundle to reproduce the issue by simply cloning it
and following some steps.
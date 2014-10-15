FR3DLdapBundle
==============

The FR3DLdapBundle adds support for provide and/or authenticate users with a
LDAP Directory in Symfony2.

It's mainly developed thinking in unmanaged corporate LDAP directories so you
could retrieve users from LDAP and manage them using `FOSUserBundle` features
(role management, guess users registration, etc).

Features include:

**Features included:**

- Works with with [FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle/)
- Customizable and extensible
- Unit tested

**Note:** This bundle cannot work as standalone at this moment and requires an User Manager (For ex: `FOSUserBundle`)

**Versions and compatibilities:**

- [![Build Status](https://secure.travis-ci.org/Maks3w/FR3DLdapBundle.png?branch=1.5.x)](http://travis-ci.org/Maks3w/FR3DLdapBundle) `1.5.x` is compatible with Symfony 2.0.x and is recommended for stable projects.
- [![Build Status](https://secure.travis-ci.org/Maks3w/FR3DLdapBundle.png?branch=1.6.x)](http://travis-ci.org/Maks3w/FR3DLdapBundle) `1.6.x` is compatible with Symfony 2.1.x and has the same features than 1.5.x
- [![Build Status](https://secure.travis-ci.org/Maks3w/FR3DLdapBundle.png?branch=2.0.x)](http://travis-ci.org/Maks3w/FR3DLdapBundle) `2.0.x` is compatible with Symfony 2.1.x and has new features. This version is actually under development and is subject to changes.
- [![Build Status](https://secure.travis-ci.org/Maks3w/FR3DLdapBundle.png?branch=master)](http://travis-ci.org/Maks3w/FR3DLdapBundle) `Master` is, at this moment, synced with 1.5.x but I encourage fix your installation to the 1.5.x version branch unless you like the risk.

Documentation
-------------

The bulk of the documentation is stored in the `Resources/doc/index.md`
file in this bundle:

[Read the Documentation](Resources/doc/index.md)

Installation
------------

All the installation instructions are located in [documentation](Resources/doc/index.md).

License
-------

This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE

Reporting an issue or a feature request
---------------------------------------

Issues and feature requests are tracked in the [GitHub issue tracker](https://github.com/Maks3w/FR3DLdapBundle/issues).

When reporting a bug, it may be a good idea to reproduce it in a basic project
built using the [Symfony Standard Edition](https://github.com/symfony/symfony-standard)
to allow developers of the bundle to reproduce the issue by simply cloning it
and following some steps.

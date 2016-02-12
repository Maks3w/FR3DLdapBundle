FR3DLdapBundle
==============

The FR3DLdapBundle adds support for provide and/or authenticate users with a
LDAP Directory in Symfony2.

It's mainly developed thinking in unmanned corporate LDAP directories so you
could retrieve users from LDAP and manage them using `FOSUserBundle` features
(role management, guess users registration, etc).

Features include:

**Features included:**

- Works with [FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle/)
- Customizable and extensible
- Unit tested

**Note:** This bundle cannot work as standalone at this moment and requires an User Manager (For ex: `FOSUserBundle`)

**Versions and compatibilities:**

- [![Build Status](https://secure.travis-ci.org/Maks3w/FR3DLdapBundle.png?branch=develop)](http://travis-ci.org/Maks3w/FR3DLdapBundle)
 `develop` contains the future major version. This version may contain new features and backward compatibility breaks.
- [![Build Status](https://secure.travis-ci.org/Maks3w/FR3DLdapBundle.png?branch=master)](http://travis-ci.org/Maks3w/FR3DLdapBundle)
 `master` contains the current major version. This version contains fixes and may contain new features.

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

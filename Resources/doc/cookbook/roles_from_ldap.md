Role Ldap Lookup
=====================

Currently two modes are supported for ldap role lookup

1. From memberOf attribute
2. Via ldap search


From memberOf attribute
-----------------------

The memberOf overlay is known supported in ActiveDirectory or OpenLdap with enabled overlay.

Further information for OpenLdap can be found in the OpenLdap documentation under "12.8. Reverse Group Membership Maintenance".
[Overlays](http://www.openldap.org/doc/admin24/overlays.html)

``` yaml
fr3d_ldap:  
    user:
        role:
            memberOf:
                dnSuffixFilter: ou=Roles,dc=example,dc=com
````


Via ldap search
-----------------------

``` yaml
fr3d_ldap:
    user:
        role:
            search:
                baseDn: ou=Roles,dc=example,dc=com
                nameAttribute: cn
                userDnAttribute: member
                userId: dn
````

A typical ldap group for the role lookup with the settings above can be look like
```
dn: cn=Admin,ou=Roles,dc=example,dc=com
description: Admin Group
gidNumber: 1001
cn: Admin
member: eif
member: cn=Test User,ou=People,dc=example,dc=com
objectClass: top
objectClass: posixGroup
```

This will result into [ROLE_ADMIN, ROLE_USER]
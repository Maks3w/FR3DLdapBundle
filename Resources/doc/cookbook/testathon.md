Open LDAP (testathon)
=====================

Here's an example configuration for authentificating against the open LDAP Testathon

``` yaml
dol_ldap:
    domains:
        # Testathon
        testathon:
            driver:
                host:                   ldap.testathon.net
                port:                   389
                username:               cn=stuart,OU=users,DC=testathon,DC=net
                password:               stuart
                bindRequiresDn:         true
                accountFilterFormat:    (cn=%s)
            user:
                baseDn:                 OU=users,DC=testathon,DC=net
                filter:                 (ObjectClass=inetOrgPerson)
                # Specify ldap attributes mapping [ldap attribute, user object method]
                attributes:
                   # Username should be the first
                   - { ldap_attr: cn,           user_method: setUsername }
                   # If you have extended user entity
                   # - { ldap_attr: mail,       user_method: setEmail }
                   # - { ldap_attr: givenname,  user_method: setFirstname }
                   # - { ldap_attr: sn,         user_method: setLastname }

````

You can log in with different accounts, like :
- stuart / stuart
- john / john
- bob / bob
- alice / alice
- ...

Explore it with a LDAP browser :)

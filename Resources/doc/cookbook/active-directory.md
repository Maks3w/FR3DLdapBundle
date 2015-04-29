Active Directory
=====================

Here's an example configuration for authenticating against the Active Directory

```yaml
fr3d_ldap:
    driver:
        host:         local.example.com
        username:     service_user@local.example.com
        password:     service_password
        accountDomainName: local.example.com
        accountDomainNameShort: LOCAL
       
    user:
        baseDn: dc=local,dc=example,dc=com
        filter: (&(ObjectClass=Person))
        attributes:
            - { ldap_attr: samaccountname,  user_method: setUsername }
```

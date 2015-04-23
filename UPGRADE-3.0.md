UPGRADE FROM 2.0 to 3.0
=======================

* If you was using the legacy (deprecated) old driver remove the `ldap_driver` setting and use the default one.

    ```yml
    # app/config/config.yml
    fr3d_ldap:
        service:
            ldap_driver: fr3d_ldap.ldap_driver.legacy # Remove this line.
    ```

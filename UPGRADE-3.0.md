UPGRADE FROM 2.0 to 3.0
=======================

* Define the attribute which hold the username value if was different than `uid`.

    Before:
    ```yml
    # app/config/config.yml
    fr3d_ldap:
        user:
            attributes:
                - { ldap_attr: customUID, user_method: setUsername }
    ```

    After:
    ```yml
    # app/config/config.yml
    fr3d_ldap:
        user:
            usernameAttribute: customUID # Add this line
            attributes:
                - { ldap_attr: customUID, user_method: setUsername }
    ```

* If you was using the legacy (deprecated) old driver remove the `ldap_driver` setting and use the default one.

    ```yml
    # app/config/config.yml
    fr3d_ldap:
        service:
            ldap_driver: fr3d_ldap.ldap_driver.legacy # Remove this line.
    ```

* If you was using `Converter` class or `LdapManagerInterface::escapeValue(string|array)`:
    Before:
    ```php
    $values = [$valueA, $valueB];
    $values = FR3D\LdapBundle\Ldap\LdapManager::escapeValue($values);
    ```

    After:
    ```php
    $filter = new FR3D\LdapBundle\Ldap\Filter\FilterValue();
    $values = [$valueA, $valueB];
    foreach($values as $key => $unescapedValue) {
        $values[$key] = $filter->filter($unescapedValue);
    }
    ```

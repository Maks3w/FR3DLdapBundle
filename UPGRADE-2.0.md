UPGRADE FROM 1.5 to 2.0
=======================

* `config.yml` has changed:

    Before:
    ```yaml
    # app/config/config.yml
    fr3d_ldap:
        client:
        version: 3
    ```

    After:
    ```yaml
    # app/config/config.yml
    fr3d_ldap:
        driver:
        bindRequiredDn: true
        #version don't exists
    ```

* If you want use the legacy (deprecated) old driver

    ```yaml
    # app/config/config.yml
    fr3d_ldap:
        #...
        service:
            ldap_driver: fr3d_ldap.ldap_driver.legacy
    ```

* `LdapManager::hydrate()` signature has changed

    Before:
    ```php
    use FR3D\LdapBundle\Model\UserInterface;
    
    protected function hydrate(LdapUserInterface $user, array $entry)
    ```
    After:
    ```php
    use Symfony\Component\Security\Core\User\UserInterface;
    
    protected function hydrate(UserInterface $user, array $entry)
    ```

* service `fr3d_ldap.client` id has changed

    Before:
    ```xml
    <service id="foo.ldap.ldap_manager" class="Foo\BarBundle\Ldap\LdapManager">
        <argument type="service" id="fr3d_ldap.client" />
        <argument type="service" id="fr3d_ldap.user_manager" />
        <argument>%fr3d_ldap.ldap_manager.parameters%</argument>
    </service>
    ```

    After:
    ```xml
    <service id="foo.ldap.ldap_manager" class="Foo\BarBundle\Ldap\LdapManager">
        <argument type="service" id="fr3d_ldap.ldap_driver" />
        <argument type="service" id="fr3d_ldap.user_manager" />
        <argument>%fr3d_ldap.ldap_manager.parameters%</argument>
    </service>
    ```

* `checkAuthentication()` now reauthenticate current user using token `getCredentials()` instead `getPassword()`

   Turn off `erase_credentials` in application `security.yml`:
   ```yml
   # app/config/security.yml
   erase_credentials: false
   ```

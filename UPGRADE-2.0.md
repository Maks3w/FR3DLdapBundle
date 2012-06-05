UPGRADE FROM 1.5 to 2.0
=======================

* config.yml has changed:

    Before:

        # app/config/config.yml
        fr3d_ldap:
            client:
            version: 3

    After

        # app/config/config.yml
        fr3d_ldap:
            driver:
            bindRequiredDn: true
            #version don't exists

* If you want use the new Zend driver (recommended)

    # deps
    [zend]
            git=git://github.com/zendframework/zf2.git

    // app/autoload.php
    $loader->registerNamespaces(array(
        // ...
        'Zend' => __DIR__.'/../zend/library',

* If you want still using old driver

    # app/config/config.yml
    fr3d_ldap:
        #...
        service:
            ldap_driver: fr3d_ldap.ldap_driver.legacy

    ));

* If you override LdapManager then:

    * hydrate definition has changed

        Before:

            use FR3D\LdapBundle\Model\UserInterface;
            //...
            protected function hydrate(LdapUserInterface $user, array $entry)(

        After:

            use Symfony\Component\Security\Core\User\UserInterface;
            //...
            protected function hydrate(UserInterface $user, array $entry)

    * service fr3d_ldap.client id has changed

        Before:

            <service id="foo.ldap.ldap_manager" class="Foo\BarBundle\Ldap\LdapManager">
                <argument type="service" id="fr3d_ldap.client" />
                <argument type="service" id="fr3d_ldap.user_manager" />
                <argument>%fr3d_ldap.ldap_manager.parameters%</argument>
            </service>

        After:

            <service id="foo.ldap.ldap_manager" class="Foo\BarBundle\Ldap\LdapManager">
                <argument type="service" id="fr3d_ldap.ldap_driver" />
                <argument type="service" id="fr3d_ldap.user_manager" />
                <argument>%fr3d_ldap.ldap_manager.parameters%</argument>
            </service>
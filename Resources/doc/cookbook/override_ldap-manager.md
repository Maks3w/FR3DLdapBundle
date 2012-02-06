Override Ldap Manager
=====================

You could easy customize Ldap Manager with a version adapted to your needs.

### Customize hydrate process

This example show how to set the mail field blank for Users object provided by
FOSUserBundle

The hydrate function fill User class attributes with the attributes retrieved
from Ldap.

**Configure LdapBundle with your service**

``` yaml
# app/config/config.yml

fr3d_ldap:
    # ...
    service:
        ldap_manager:  acme.ldap.ldap_manager
````

**Setup the service in your own bundle**

```` xml
<!-- src/Acme/DemoBundle/Resources/config/services.xml -->
<container xmlns="http://symfony.com/schema/dic/services"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <services>

        <!-- ... -->

        <service id="acme.ldap.ldap_manager" class=Acme\DemoBundle\Ldap\LdapManager">
            <argument type="service" id="fr3d_ldap.client" />
            <argument type="service" id="fr3d_ldap.user_manager" />
            <argument>%fr3d_ldap.ldap_manager.parameters%</argument>
        </service>

        <!-- ... -->

    </services>

</container>
````

**Extends LdapManager and customize him**

```` php
// src/Acme/DemoBundle/Ldap/LdapManager.php
<?php

namespace Acme\DemoBundle\Ldap;

use FR3D\LdapBundle\Ldap\LdapManager as BaseLdapManager;
use FR3D\LdapBundle\Model\LdapUserInterface;

class LdapManager extends BaseLdapManager
{
    protected function hydrate(LdapUserInterface $user, array $entry)
    {
        parent::hydrate($user, $entry);

        // Your custom code
        $user->setEmail('');
        $user->setEmailCanonical('');
    }
}
````

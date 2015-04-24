Use custom hydrator
===================

You could create a custom user hydrator for populate your user object with your custom needs.

### Customize hydrate process

This example show how to set the mail field blank for Users object provided by FOSUserBundle

The function method fill User class attributes with the attributes retrieved from Ldap.

**Configure Fr3dLdapBundle with your service**

```yaml
# app/config/config.yml

fr3d_ldap:
    # ...
    service:
        user_hydrator: acme.ldap.user_hydrator
```

**Setup the service in your own bundle**

```xml
<!-- src/Acme/DemoBundle/Resources/config/services.xml -->
<container xmlns="http://symfony.com/schema/dic/services"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <services>

        <!-- ... -->

        <service id="acme.ldap.user_hydrator" class="Acme\DemoBundle\Ldap\UserHydrator">
        </service>

        <!-- ... -->

    </services>

</container>
```

**Implement HydratorInterface**

```php
// src/Acme/DemoBundle/Ldap/UserHydrator.php
<?php

namespace Acme\DemoBundle\Ldap;

use FR3D\LdapBundle\Hydrator\HydratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class LdapManager implements HydratorInterface
{
    /**
     * Populate an user with the data retrieved from LDAP.
     *
     * @param array $ldapUserAttributes
     *
     * @return UserInterface
     */
    public function hydrate(array $entry)
    {
        $user = new \Acme\DemoBundle\Entity\User();
        $user->setUsername($ldapUserAttributes['uid'][0]);
        $user->setEmail($ldapUserAttributes['email'][0]);

        return $user;
    }
}
```

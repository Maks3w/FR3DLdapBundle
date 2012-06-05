Prevent registrations with usernames that already exists on LDAP
================================================================

Since that FR3DLdapBundle can be used together with third party other user
managers like FOSUserBundle, could be possible that an user register himself
with an username that already exists on LDAP.

To prevent this behavior, FR3DLdapBundle is shipped with a validator for assure
the uniqueness on the username field. This validator need be enabled with one
easy step in your bundle.

### Enable Unique constraint for Username attribute

**Using validation.xml**

If you prefer XML format put this code on validation.xml and set the class name
to the correct one.

```` xml
<!-- src/Acme/BlogBundle/Resources/config/validation.xml -->
<?xml version="1.0" ?>
<constraint-mapping xmlns="http://symfony.com/schema/dic/constraint-mapping"
                    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                    xsi:schemaLocation="http://symfony.com/schema/dic/constraint-mapping http://symfony.com/schema/dic/constraint-mapping/constraint-mapping-1.0.xsd">

    <class name="Acme\DemoBundle\Entity\User">
        <constraint name="FR3D\LdapBundle\Validator\Unique">
            <option name="property">username</option>
            <option name="message">fr3d_ldap.username.already_used</option>
            <option name="groups">
                <value>Registration</value>
                <value>Profile</value>
            </option>
        </constraint>
    </class>

</constraint-mapping>
```

NOTE: At the moment of write this recipe, the validator only can check uniqueness with the username attribute

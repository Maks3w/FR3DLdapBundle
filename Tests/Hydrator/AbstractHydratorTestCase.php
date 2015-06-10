<?php

namespace FR3D\LdapBundle\Tests\Hydrator;

abstract class AbstractHydratorTestCase extends \PHPUnit_Framework_TestCase
{
    use HydratorTestTrait;

    protected $ldapManagerParameters = [
        'baseDn' => 'ou=Persons,dc=example,dc=com',
        'filter' => '',
        'usernameAttribute' => 'uid',
        'attributes' => [
            [
                'ldap_attr' => 'uid',
                'user_method' => 'setUsername',
            ],
            [
                'ldap_attr' => 'roles',
                'user_method' => 'setRoles',
            ],
        ],
    ];
}

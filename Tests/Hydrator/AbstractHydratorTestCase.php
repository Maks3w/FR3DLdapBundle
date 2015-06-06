<?php

namespace FR3D\LdapBundle\Tests\Hydrator;

abstract class AbstractHydratorTestCase extends \PHPUnit_Framework_TestCase
{
    use HydratorTestTrait;

    protected $attributeMap = [
        [
            'ldap_attr' => 'uid',
            'user_method' => 'setUsername',
        ],
        [
            'ldap_attr' => 'roles',
            'user_method' => 'setRoles',
        ],
    ];
}

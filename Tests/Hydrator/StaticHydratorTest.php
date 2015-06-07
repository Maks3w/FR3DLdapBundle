<?php

namespace FR3D\LdapBundle\Tests\Hydrator;

use FR3D\LdapBundle\Hydrator\StaticHydrator;

class StaticHydratorTest extends AbstractHydratorTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $attributeMap = [
            [
                'ldap_attr' => 'uid',
                'user_method' => 'setUsername',
            ],
            [
                'ldap_attr' => 'roles',
                'user_method' => 'setRoles',
            ],
        ];

        $params = [
            'user_class' => 'FR3D\LdapBundle\Model\LdapUser',
        ];

        $this->hydrator = new StaticHydrator($params, $attributeMap);
    }
}

<?php

namespace FR3D\LdapBundle\Tests\Hydrator;

use FR3D\LdapBundle\Hydrator\StaticHydrator;

class StaticHydratorTest extends AbstractHydratorTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $params = [
            'user_class' => 'FR3D\LdapBundle\Model\LdapUser',
        ];

        $this->hydrator = new StaticHydrator($params, $this->getDefaultUserConfig());
    }
}

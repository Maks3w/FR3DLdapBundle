<?php

namespace FR3D\LdapBundle\Tests\Ldap\Filter;

use FR3D\LdapBundle\Ldap\Filter\FilterValue;

class FilterValueTest extends \PHPUnit_Framework_TestCase
{
    use FilterInterfaceTestTrait;

    protected function setUp()
    {
        parent::setUp();

        $this->filter = new FilterValue();
    }

    public function validValuesDataProvider()
    {
        return [
            // Description => [mixed $input, mixed $filteredInput]
            'null' => [
                'input' => null,
                'filtered' => '',
            ],
            'empty' => [
                'input' => '',
                'filtered' => '',
            ],
            'special characters' => [
                'input' => 't(e,s)t*v\\a' . chr(31) . 'l' . chr(15) . 'u' . chr(0) . 'e',
                'filtered' => 't\28e,s\29t\2av\5ca' . chr(31) . 'l' . chr(15) . 'u\00e',
            ],
            'ASCII' => [
                'input' => 'azAZ09._$%',
                'filtered' => 'azAZ09._$%',
            ],
            'utf-8' => [
                'input' => 'ÄÖÜäöüß€',
                'filtered' => 'ÄÖÜäöüß€',
            ],
        ];
    }
}

<?php

namespace FR3D\LdapBundle\Tests\Ldap\Filter;

use FR3D\LdapBundle\Ldap\Filter\FilterInterface;
use PHPUnit_Framework_Assert as Assert;

/**
 * Test cases for FilterInterface.
 */
trait FilterTestTrait
{
    /**
     * @var FilterInterface
     */
    protected $filter;

    /**
     * Valid values data provider.
     *
     * Returns an array with the form:
     * [
     *   // Description => [mixed $input, mixed $filteredInput]
     * ]
     *
     * @return mixed[][]
     */
    abstract public function validValuesDataProvider();

    /**
     * Test filter() method filter valid values as expected.
     *
     * @dataProvider validValuesDataProvider
     *
     * @param mixed $inputValue
     * @param mixed $expectedOutput
     *
     * @return void
     */
    public function testFilterValidValues($inputValue, $expectedOutput)
    {
        Assert::assertEquals($expectedOutput, $this->filter->filter($inputValue));
    }
}

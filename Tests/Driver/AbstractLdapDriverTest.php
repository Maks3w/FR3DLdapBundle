<?php

namespace FR3D\LdapBundle\Tests\Driver;

abstract class AbstractLdapDriverTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!function_exists('ldap_connect')) {
            $this->markTestSkipped('PHP LDAP extension not loaded');
        }
    }

    protected function getOptions()
    {
        $options = [
            'host' => 'ldap.example.com',
            'port' => 389,
            'useStartTls' => true,
        ];

        return $options;
    }
}

<?php

namespace FR3D\LdapBundle\Tests\Model;

use FR3D\LdapBundle\Model\LdapUserManager;

class LdapUserManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var array */
    protected $params;

    /**
     * @var \FR3D\LdapBundle\Model\LdapUserInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ldapUser;
    
    /**
     * @var LdapUserManager
     */
    protected $ldapUserManager;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->params = array(
            'user_class'     => 'FR3D\LdapBundle\Model\LdapUser',
        );

        $this->ldapUser = $this->getMock('FR3D\LdapBundle\Model\LdapUserInterface');
        
        $this->ldapUserManager = new LdapUserManager($this->params);
    }

    /**
     * @covers FR3D\LdapBundle\Model\LdapUserManager::__construct
     */
    public function testConstruct()
    {
        $this->ldapUserManager = new LdapUserManager($this->params);

        $reflectionClass        = new \ReflectionClass('FR3D\LdapBundle\Model\LdapUserManager');
        $propertyParams = $reflectionClass->getProperty('params');
        $propertyParams->setAccessible(true);

        $this->assertEquals($this->params, $propertyParams->getValue($this->ldapUserManager));
    }

    /**
     * @covers FR3D\LdapBundle\Model\LdapUserManager::createUser
     */
    public function testCreateUser()
    {
        $ldapUser = $this->ldapUserManager->createUser();

        $this->assertInstanceOf('FR3D\LdapBundle\Model\LdapUser', new $ldapUser);
    }
}

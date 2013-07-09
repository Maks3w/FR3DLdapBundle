<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FR3D\LdapBundle\Tests\Validation;

use FR3D\LdapBundle\Validator\UniqueValidator;
use FR3D\LdapBundle\Validator\Unique;
use FR3D\LdapBundle\Tests\TestUser;

class UniqueValidatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var UniqueValidator */
    private $validator;
    /** @var ExecutionContext */
    private $context;
    /** @var \FR3D\LdapBundle\Ldap\LdapManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $ldapManagerMock;
    /** @var Unique */
    private $constraint;
    /** @var TestUser */
    private $user;

    public function setUp()
    {
        $this->context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContext')
                ->disableOriginalConstructor()
                ->getMock();

        $this->ldapManagerMock = $this->getMock('FR3D\LdapBundle\Ldap\LdapManagerInterface');
        $this->constraint = new Unique(array('username'));
        $this->validator = new UniqueValidator($this->ldapManagerMock);
        $this->validator->initialize($this->context);

        $this->user = new TestUser();
    }

    public function testViolationsOnDuplicateUserProperty()
    {
        $this->ldapManagerMock->expects($this->once())
                ->method('findUserByUsername')
                ->will($this->returnValue($this->user))
                ->with($this->equalTo($this->user->getUsername()));

        $this->validator->validate($this->user, $this->constraint);

        $this->assertEquals(1, $this->context->getViolations()->count());
    }

    public function testNoViolationsOnUniqueUserProperty()
    {
        $this->ldapManagerMock->expects($this->once())
                ->method('findUserByUsername')
                ->will($this->returnValue(null))
                ->with($this->equalTo($this->user->getUsername()));

        $this->validator->validate($this->user, $this->constraint);
        
        $this->assertEquals(0, $this->context->getViolations()->count());
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testBadType()
    {
        $this->validator->validate('bad_type', $this->constraint);
    }
}

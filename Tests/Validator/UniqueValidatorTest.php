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

use FR3D\LdapBundle\Tests\TestUser;
use FR3D\LdapBundle\Validator\Unique;
use FR3D\LdapBundle\Validator\UniqueValidator;
use Symfony\Component\Validator\ExecutionContextInterface;

class UniqueValidatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var UniqueValidator */
    private $validator;
    /** @var ExecutionContextInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $validatorContext;
    /** @var \FR3D\LdapBundle\Ldap\LdapManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $ldapManagerMock;
    /** @var Unique */
    private $constraint;
    /** @var TestUser */
    private $user;

    public function setUp()
    {
        $this->validatorContext = $this->getMock('Symfony\Component\Validator\ExecutionContextInterface');

        $this->ldapManagerMock = $this->getMock('FR3D\LdapBundle\Ldap\LdapManagerInterface');
        $this->constraint = new Unique(array('username'));
        $this->validator = new UniqueValidator($this->ldapManagerMock);
        $this->validator->initialize($this->validatorContext);

        $this->user = new TestUser();
    }

    public function testViolationsOnDuplicateUserProperty()
    {
        $this->ldapManagerMock->expects($this->once())
                ->method('findUserByUsername')
                ->will($this->returnValue($this->user))
                ->with($this->equalTo($this->user->getUsername()));

        $this->validatorContext->expects($this->once())
                ->method('addViolation')
                ->with($this->constraint->message, array('%property%' => $this->constraint->property));

        $this->validator->validate($this->user, $this->constraint);
    }

    public function testNoViolationsOnUniqueUserProperty()
    {
        $this->ldapManagerMock->expects($this->once())
                ->method('findUserByUsername')
                ->will($this->returnValue(null))
                ->with($this->equalTo($this->user->getUsername()));

        $this->validatorContext->expects($this->never())
                ->method('addViolation');

        $this->validator->validate($this->user, $this->constraint);
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testBadType()
    {
        $this->validator->validate('bad_type', $this->constraint);
    }
}

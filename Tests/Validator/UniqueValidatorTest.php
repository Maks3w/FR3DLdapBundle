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
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraint;
use FR3D\LdapBundle\Ldap\LdapManagerInterface;

/**
 * @covers \FR3D\LdapBundle\Validator\Unique
 * @covers \FR3D\LdapBundle\Validator\UniqueValidator
 */
class UniqueValidatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var UniqueValidator */
    private $validator;
    /** @var ExecutionContextInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $validatorContext;
    /** @var LdapManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $ldapManagerMock;
    /** @var Unique */
    private $constraint;
    /** @var TestUser */
    private $user;

    public function setUp(): void
    {
        $this->validatorContext = $this->getMock(ExecutionContextInterface::class);
        $this->ldapManagerMock = $this->getMock(LdapManagerInterface::class);
        $this->constraint = new Unique();
        $this->validator = new UniqueValidator($this->ldapManagerMock);
        $this->validator->initialize($this->validatorContext);

        $this->user = new TestUser();
        $this->user->setUsername('fooName');
    }

    public function testViolationsOnDuplicateUserProperty(): void
    {
        $this->ldapManagerMock->expects($this->once())
                ->method('findUserByUsername')
                ->will($this->returnValue($this->user))
                ->with($this->equalTo($this->user->getUsername()));

        $this->validatorContext->expects($this->once())
                ->method('addViolation')
                ->with('User already exists.');

        $this->validator->validate($this->user, $this->constraint);
    }

    public function testNoViolationsOnUniqueUserProperty(): void
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
    public function testBadType(): void
    {
        /* @noinspection PhpParamsInspection */
        $this->validator->validate('bad_type', $this->constraint);
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testWrongConstraint(): void
    {
        /* @noinspection PhpParamsInspection */
        $this->validator->validate($this->user, $this->getMock(Constraint::class));
    }
}

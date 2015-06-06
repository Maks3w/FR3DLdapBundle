<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FR3D\LdapBundle\Validator;

use FR3D\LdapBundle\Ldap\LdapManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * UniqueValidator.
 */
class UniqueValidator extends ConstraintValidator
{
    /**
     * @var LdapManagerInterface
     */
    protected $ldapManager;

    /**
     * Constructor.
     *
     * @param LdapManagerInterface $ldapManager
     */
    public function __construct(LdapManagerInterface $ldapManager)
    {
        $this->ldapManager = $ldapManager;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param UserInterface $value      The value that should be validated
     * @param Constraint    $constraint The constraint for the validation
     *
     * @throws UnexpectedTypeException if $value is not instance of \Symfony\Component\Security\Core\User\UserInterface
     * @throws UnexpectedTypeException if $constraint is not instance of \FR3D\LdapBundle\Validator\Unique
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Unique) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\Unique');
        }

        if (!$value instanceof UserInterface) {
            throw new UnexpectedTypeException($value, 'Symfony\Component\Security\Core\User\UserInterface');
        }

        $user = $this->ldapManager->findUserByUsername($value->getUsername());

        if ($user) {
            $this->context->addViolation($constraint->message);
        }
    }
}

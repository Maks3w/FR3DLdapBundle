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

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Security\Core\User\UserInterface;
use FR3D\LdapBundle\Ldap\LdapManagerInterface;

/**
 * UniqueValidator
 */
class UniqueValidator extends ConstraintValidator
{
    /**
     * @var LdapManagerInterface
     */
    protected $ldapManager;

    /**
     * Constructor
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
     * @param Constraint    $constraint The constrain for the validation
     *
     * @return Boolean Whether or not the value is valid
     *
     * @throws UnexpectedTypeException if $value is not instance of \Symfony\Component\Security\Core\User\UserInterface
     */
    public function isValid($value, Constraint $constraint)
    {
        if (!$value instanceof UserInterface) {
            throw new UnexpectedTypeException($value, 'Symfony\Component\Security\Core\User\UserInterface');
        }

        $user = $this->ldapManager->findUserByUsername($value->getUsername());

        if ($user) {
            $this->setMessage($constraint->message, array(
                '%property%' => $constraint->property
            ));

            return false;
        }

        return true;
    }
}

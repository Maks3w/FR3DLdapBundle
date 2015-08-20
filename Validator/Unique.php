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

/**
 * Constraint for the Unique validator.
 *
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class Unique extends Constraint
{
    public $message = 'User already exists.';

    public function validatedBy()
    {
        return 'fr3d_ldap.validator.unique';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

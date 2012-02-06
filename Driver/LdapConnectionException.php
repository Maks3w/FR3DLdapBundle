<?php

namespace FR3D\LdapBundle\Driver;

class LdapConnectionException extends \Exception
{

    public function __construct($message)
    {
        parent::__construct($message);
    }
}
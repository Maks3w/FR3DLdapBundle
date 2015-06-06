<?php

namespace FR3D\LdapBundle\Ldap\Filter;

/**
 * Interface for filter classes.
 *
 * Filtering is useful for sanitize input values.
 */
interface FilterInterface
{
    /**
     * Returns the result of filtering $value.
     *
     * @param mixed $value
     *
     * @throws \RuntimeException If filtering $value is impossible
     *
     * @return mixed
     */
    public function filter($value);
}

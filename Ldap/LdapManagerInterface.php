<?php

namespace FR3D\LdapBundle\Ldap;

use FR3D\LdapBundle\Model\LdapUserInterface;

interface LdapManagerInterface
{

    /**
     * Find a user by its username.
     *
     * @param  string $username
     * @return \Symfony\Component\Security\Core\User\UserInterface|null The user or null if the user does not exist
     */
    public function findUserByUsername($username);

    /**
     * Finds one user by the given criteria.
     *
     * @param  array  $criteria
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function findUserBy(array $criteria);

    /**
     * Bind the user on ldap
     *
     * @param  LdapUserInterface $user
     * @param  string            $password
     * @return Boolean
     */
    public function bind(LdapUserInterface $user, $password);

    /**
     * Escapes the given VALUES according to RFC 2254 so that they can be safely used in LDAP filters.
     *
     * Any control characters with an ASCII code < 32 as well as the characters with special meaning in
     * LDAP filters "*", "(", ")", and "\" (the backslash) are converted into the representation of a
     * backslash followed by two hex digits representing the hexadecimal value of the character.
     * @see Net_LDAP2_Util::escape_filter_value() from Benedikt Hallinger <beni@php.net>
     * @link http://pear.php.net/package/Net_LDAP2
     *
     * @param  string|array $values Array of values to escape
     * @return array Array $values, but escaped
     */
    public static function escapeValue($values = array());

    /**
     * Undoes the conversion done by {@link escapeValue()}.
     *
     * Converts any sequences of a backslash followed by two hex digits into the corresponding character.
     * @see Net_LDAP2_Util::escape_filter_value() from Benedikt Hallinger <beni@php.net>
     * @link http://pear.php.net/package/Net_LDAP2
     *
     * @param  string|array $values Array of values to escape
     * @return array Array $values, but unescaped
     */
    public static function unescapeValue($values = array());
}

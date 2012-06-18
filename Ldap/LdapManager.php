<?php

namespace FR3D\LdapBundle\Ldap;

use FR3D\LdapBundle\Driver\LdapConnectionInterface;
use FR3D\LdapBundle\Model\LdapUserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

class LdapManager implements LdapManagerInterface
{
    private $connection;
    private $userManager;
    private $params = array();
    private $ldapAttributes = array();
    private $ldapUsernameAttr;

    public function __construct(LdapConnectionInterface $connection, $userManager, array $params)
    {
        $this->connection = $connection;
        $this->userManager = $userManager;
        $this->params = $params;

        foreach ($this->params['attributes'] as $attr) {
            $this->ldapAttributes[] = $attr['ldap_attr'];
        }

        $this->ldapUsernameAttr = $this->ldapAttributes[0];
    }

    /**
     * {@inheritDoc}
     */
    public function findUserByUsername($username)
    {
        return $this->findUserBy(array($this->ldapUsernameAttr => $username));
    }

    /**
     * {@inheritDoc}
     */
    public function findUserBy(array $criteria)
    {
        $filter  = $this->buildFilter($criteria);
        $entries = $this->connection->search($this->params['baseDn'], $filter, $this->ldapAttributes);

        if ($entries['count'] > 1) {
            throw new \Exception('This search can only return a single user');
        }

        if ($entries['count'] == 0) {
            return false;
        }
        $user = $this->userManager->createUser();
        $this->hydrate($user, $entries[0]);

        return $user;
    }

    /**
     * Build Ldap filter
     *
     * @param  array  $criteria
     * @param  string $condition
     * @return string
     */
    private function buildFilter(array $criteria, $condition = '&')
    {
        $criteria = self::escapeValue($criteria);
        $filters = array();
        $filters[] = $this->params['filter'];
        foreach ($criteria as $key => $value) {
            $filters[] = sprintf('(%s=%s)', $key, $value);
        }

        return sprintf('(%s%s)', $condition, implode($filters));
    }

    /**
     * Hydrates an user entity with ldap attributes.
     *
     * @param LdapUserInterface $user  user to hydrate
     * @param array             $entry ldap result
     *
     * @return LdapUserInterface
     */
    protected function hydrate(LdapUserInterface $user, array $entry)
    {
        $user->setPassword('');

        if ($user instanceof AdvancedUserInterface) {
            $user->setEnabled(true);
        }

        foreach ($this->params['attributes'] as $attr) {
            $ldapValue = $entry[$attr['ldap_attr']];
            $value = null;

            if ((array_key_exists('count', $ldapValue) &&  $ldapValue['count'] == 1)
                    || !array_key_exists('count', $ldapValue)) {

                $value = $ldapValue[0];
            } else {
                $value = array_slice($ldapValue, 1);
            }

            call_user_func(array($user, $attr['user_method']), $value);
        }

        $user->setDn($entry['dn']);
    }

    /**
     * {@inheritDoc}
     */
    public function bind(LdapUserInterface $user, $password)
    {
        return $this->connection->bind($user->getDn(), $password);
    }

    /**
     * Escapes the given VALUES according to RFC 2254 so that they can be safely used in LDAP filters.
     *
     * Any control characters with an ASCII code < 32 as well as the characters with special meaning in
     * LDAP filters "*", "(", ")", and "\" (the backslash) are converted into the representation of a
     * backslash followed by two hex digits representing the hexadecimal value of the character.
     * @see Net_LDAP2_Util::escape_filter_value() from Benedikt Hallinger <beni@php.net>
     * @link http://pear.php.net/package/Net_LDAP2
     * @author Benedikt Hallinger <beni@php.net>
     *
     * @param  string|array $values Array of values to escape
     * @return array Array $values, but escaped
     */
    public static function escapeValue($values = array())
    {
        if (!is_array($values))
            $values = array($values);
        foreach ($values as $key => $val) {
            // Escaping of filter meta characters
            $val = str_replace(array('\\', '*', '(', ')'), array('\5c', '\2a', '\28', '\29'), $val);
            // ASCII < 32 escaping
            $val = Converter::ascToHex32($val);
            if (null === $val) {
                $val          = '\0';  // apply escaped "null" if string is empty
            }
            $values[$key] = $val;
        }

        return (count($values) == 1 && array_key_exists(0, $values)) ? $values[0] : $values;
    }

    /**
     * Undoes the conversion done by {@link escapeValue()}.
     *
     * Converts any sequences of a backslash followed by two hex digits into the corresponding character.
     * @see Net_LDAP2_Util::escape_filter_value() from Benedikt Hallinger <beni@php.net>
     * @link http://pear.php.net/package/Net_LDAP2
     * @author Benedikt Hallinger <beni@php.net>
     *
     * @param  string|array $values Array of values to escape
     * @return array Array $values, but unescaped
     */
    public static function unescapeValue($values = array())
    {
        if (!is_array($values))
            $values = array($values);
        foreach ($values as $key => $value) {
            // Translate hex code into ascii
            $values[$key] = Converter::hex32ToAsc($value);
        }

        return (count($values) == 1 && array_key_exists(0, $values)) ? $values[0] : $values;
    }
}

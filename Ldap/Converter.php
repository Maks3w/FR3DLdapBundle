<?php

namespace FR3D\LdapBundle\Ldap;

/**
 * Converter is a collection of useful LDAP related conversion functions.
 *
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Converter
{
    /**
     * Converts all ASCII chars < 32 to "\HEX".
     *
     * @see Net_LDAP2_Util::asc2hex32() from Benedikt Hallinger <beni@php.net>
     * @link http://pear.php.net/package/Net_LDAP2
     *
     * @author Benedikt Hallinger <beni@php.net>
     *
     * @param  string $string String to convert
     *
     * @return string
     */
    public static function ascToHex32($string)
    {
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            if (ord($char) < 32) {
                $hex    = dechex(ord($char));
                if (strlen($hex) == 1) {
                    $hex    = '0' . $hex;
                }
                $string = str_replace($char, '\\' . $hex, $string);
            }
        }

        return $string;
    }

    /**
     * Converts all Hex expressions ("\HEX") to their original ASCII characters.
     *
     * @see Net_LDAP2_Util::hex2asc() from Benedikt Hallinger <beni@php.net>,
     * heavily based on work from DavidSmith@byu.net
     * @link http://pear.php.net/package/Net_LDAP2
     *
     * @author Benedikt Hallinger <beni@php.net>, heavily based on work from DavidSmith@byu.net
     *
     * @param  string $string String to convert
     *
     * @return string
     */
    public static function hex32ToAsc($string)
    {
        $string = preg_replace_callback(
            "/\\\([0-9A-Fa-f]{2})/",
            function ($matches) { return chr(hexdec($matches[1])); },
            $string
        );

        return $string;
    }
}

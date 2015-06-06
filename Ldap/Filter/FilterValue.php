<?php

namespace FR3D\LdapBundle\Ldap\Filter;

/**
 * Escapes the LDAP Filter value according to RFC 4515.
 *
 * Ensures that the entire filter string is a valid UTF-8 string and
 * provides that the octets that represent the ASCII characters "*"
 * (ASCII 0x2a), "(" (ASCII 0x28), ")" (ASCII 0x29), "\" (ASCII 0x5c), and
 * NUL (ASCII 0x00) are represented as a backslash "\" (ASCII 0x5c) followed
 * by the two hexadecimal digits representing the value of the encoded octet.
 *
 * @see http://tools.ietf.org/search/rfc4515#section-3 RFC 4515
 */
class FilterValue implements FilterInterface
{
    public function filter($value)
    {
        if (!function_exists('ldap_escape')) {
            require_once __DIR__ . '/ldap_escape.php';
        }

        /*
         * valueencoding  = 0*(normal / escaped)
         * normal         = UTF1SUBSET / UTFMB
         * escaped        = ESC HEX HEX
         * UTF1SUBSET     = %x01-27 / %x2B-5B / %x5D-7F
         *                  ; UTF1SUBSET excludes 0x00 (NUL), LPAREN,
         *                  ; RPAREN, ASTERISK, and ESC.
         * EXCLAMATION    = %x21 ; exclamation mark ("!")
         * AMPERSAND      = %x26 ; ampersand (or AND symbol) ("&")
         * ASTERISK       = %x2A ; asterisk ("*")
         * COLON          = %x3A ; colon (":")
         * VERTBAR        = %x7C ; vertical bar (or pipe) ("|")
         * TILDE          = %x7E ; tilde ("~")
         *
         * The <valueencoding> rule ensures that the entire filter string is a
         * valid UTF-8 string and provides that the octets that represent the
         * ASCII characters "*" (ASCII 0x2a), "(" (ASCII 0x28), ")" (ASCII
         * 0x29), "\" (ASCII 0x5c), and NUL (ASCII 0x00) are represented as a
         * backslash "\" (ASCII 0x5c) followed by the two hexadecimal digits
         * representing the value of the encoded octet.
         *
         * http://tools.ietf.org/search/rfc4515#section-3
         */
        return ldap_escape($value, '', LDAP_ESCAPE_FILTER);
    }
}

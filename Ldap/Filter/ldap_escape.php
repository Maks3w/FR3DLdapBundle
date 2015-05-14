<?php

/**
 * Polyfill for PHP 5.6 ldap_escape function.
 *
 * @see http://php.net/manual/en/function.ldap-escape.php
 */
if (!function_exists('ldap_escape')) {
    define('LDAP_ESCAPE_FILTER', 0x01);
    define('LDAP_ESCAPE_DN', 0x02);

    /**
     * @param string $subject The subject string
     * @param string $ignore Set of characters to leave untouched
     * @param int $flags Any combination of LDAP_ESCAPE_* flags to indicate the
     *                   set(s) of characters to escape.
     *
     * @return string
     */
    function ldap_escape($subject, $ignore = '', $flags = 0)
    {
        static $charMaps = [
            LDAP_ESCAPE_FILTER => ['\\', '*', '(', ')', "\x00"],
            LDAP_ESCAPE_DN => ['\\', ',', '=', '+', '<', '>', ';', '"', '#'],
        ];

        // Pre-process the char maps on first call
        if (!isset($charMaps[0])) {
            $charMaps[0] = [];
            for ($i = 0; $i < 256; $i++) {
                $charMaps[0][chr($i)] = sprintf('\\%02x', $i);
            }

            for ($i = 0, $l = count($charMaps[LDAP_ESCAPE_FILTER]); $i < $l; $i++) {
                $chr = $charMaps[LDAP_ESCAPE_FILTER][$i];
                unset($charMaps[LDAP_ESCAPE_FILTER][$i]);
                $charMaps[LDAP_ESCAPE_FILTER][$chr] = $charMaps[0][$chr];
            }

            for ($i = 0, $l = count($charMaps[LDAP_ESCAPE_DN]); $i < $l; $i++) {
                $chr = $charMaps[LDAP_ESCAPE_DN][$i];
                unset($charMaps[LDAP_ESCAPE_DN][$i]);
                $charMaps[LDAP_ESCAPE_DN][$chr] = $charMaps[0][$chr];
            }
        }

        // Create the base char map to escape
        $flags = (int) $flags;
        $charMap = [];
        if ($flags & LDAP_ESCAPE_FILTER) {
            $charMap += $charMaps[LDAP_ESCAPE_FILTER];
        }
        if ($flags & LDAP_ESCAPE_DN) {
            $charMap += $charMaps[LDAP_ESCAPE_DN];
        }
        if (!$charMap) {
            $charMap = $charMaps[0];
        }

        // Remove any chars to ignore from the list
        $ignore = (string) $ignore;
        for ($i = 0, $l = strlen($ignore); $i < $l; $i++) {
            unset($charMap[$ignore[$i]]);
        }

        // Do the main replacement
        $result = strtr($subject, $charMap);

        // Encode leading/trailing spaces if LDAP_ESCAPE_DN is passed
        if ($flags & LDAP_ESCAPE_DN) {
            if ($result[0] === ' ') {
                $result = '\\20' . substr($result, 1);
            }
            if ($result[strlen($result) - 1] === ' ') {
                $result = substr($result, 0, -1) . '\\20';
            }
        }

        return $result;
    }
}

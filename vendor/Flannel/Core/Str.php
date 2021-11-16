<?php

namespace Flannel\Core;

/**
 * String helper
 *
 */
class Str {

    /**
     *
     * @param  string $str       String
     * @param  int    $length    Desired length of the truncated string
     * @param  string $substring The substring to append if it can fit
     * @return self
     */
    public static function truncateWithTooltip($str, $length, $substring='…') {
        if(mb_strlen($str) > $length) {
            $str = '<span data-popup="tooltip" title="' . static::htmlAttr($str) . '">' . self::htmlText(mb_substr($str, 0, $length) . $substring) . '</span>';
        }
        return $str;
    }

    /**
     * Converts any string into an HTML-safe ID
     *
     * @param string $str
     * @return string
     */
    public static function htmlId($str) {
        return preg_replace('/[\s-]+/', '-', preg_replace('/[^\w\s-\.]/', '', strtolower(trim($str))));
    }

    /**
     * Escape quotes inside html attributes
     *
     * @param string $str
     * @param bool $addSlashes
     * @return string
     */
    public static function htmlAttr($str, $addSlashes=false) {
        if ($addSlashes) {
            $str = addslashes($str);
        }
        return htmlspecialchars($str, ENT_QUOTES, null, false);
    }

    /**
     * Sanitize output for HTML
     *
     * @param string $str
     * @return string
     */
    public static function htmlText($str) {
        return htmlspecialchars($str, ENT_QUOTES, null, false);
    }

    /**
     * Hash, compressed from 16-bit to 64-bit
     *
     * For example:
     * sha1('foo bar'): 3773dea65156909838fa6c22825cafe090ff8030 (40 chars)
     * shortHash('foo bar'): N3PeplFWkJg4-mwiglyv4JD_gDA (27 chars)
     *
     * Output is URL-safe
     *
     * @param mixed $val
     * @param string $algo
     * @return string
     */
    public static function shortHash($val, $algo='sha1') {
        return strtr(rtrim(base64_encode(hash($algo, $val, true)), '='), '+/', '-_');
    }

    /**
     * Generates cryptographically secure pseudo-random bytes
     * that is converted to a 64-bit hash
     *
     * @param int $bytes (not str length)
     * @return string
     */
    public static function random($bytes=32) {
        return static::shortHash(bin2hex(random_bytes($bytes)));
    }

}

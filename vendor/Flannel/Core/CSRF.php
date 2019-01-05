<?php

namespace Flannel\Core;

/**
 * CSRF Protection
 *
 * HTML Form usage:
 * - Add `echo \Flannel\Core\CSRF::getHiddenInputHtml()` to your HTML forms
 * - Add `$this->_validateCSRF();` to the top of your controller action
 *
 * AJAX usages:
 * - Pass the token from `\Flannel\Core\CSRF::getToken()` as POST data
 * - If the POST data is named \Flannel\Core\CSRF::INPUT_NAME, you can use `$this->_validateCSRF();` in your controller
 *   Otherwise, simply call `\Flannel\Core\CSRF::validate($token)` and handle the boolean response as needed
 */
class CSRF {

    const SESSION_NAMESPACE = 'csrf';
    const SESSION_NAME = 'token';
    const INPUT_NAME = self::SESSION_NAMESPACE . '-' . self::SESSION_NAME;

    /**
     * Get CSRF token (with lazy-loaded generation)
     *
     * @return string
     */
    public static function getToken() {
        $token = \Flannel\Core\Session::get(self::SESSION_NAME, self::SESSION_NAMESPACE);
        if(!$token) {
            $token = \Flannel\Core\Str::random(32);
            \Flannel\Core\Session::set(self::SESSION_NAME, $token, self::SESSION_NAMESPACE);
        }
        return $token;
    }

    /**
     *
     * @param string|null $token
     * @return bool
     */
    public static function validate($token=null) {
        if($token === null) {
            $token = \Flannel\Core\Input::request(self::INPUT_NAME);
        }
        return $token === self::getToken();
    }

    /**
     * Shortcut method for generating hidden input field
     *
     * @return string
     */
    public static function getHiddenInputHtml() {
        return '<input type="hidden" name="' . self::INPUT_NAME . '" value="' . self::getToken() . '">';
    }

}

<?php

namespace Flannel\Core;

\Flannel\Core\Config::required(['env.https.enforce','session.handler','session.savepath']);

class Session {

    const MAX_AGE = SECONDS_PER_HOUR / 2;

    const NAMESPACE_DEFAULT = '__DEFAULT';
    const NAMESPACE_INTERNAL = '__INTERNAL';

    /**
     * @throws Exception
     */
    public static function start() {
        self::_iniConfig();
        
        session_start();

        if(!self::_validate()) {
            session_abort();
            $_SESSION = array();
            $params = session_get_cookie_params();
            \Flannel\Core\Cookie::delete(session_name(), $params['path'], $params['domain'], $params['secure'], $params['httponly']);
            throw new Exception('Session expired or otherwise could not be validated');
        }
    }

    /**
     * @return bool
     */
    protected static function _validate() {
        $lastActive = (int)self::get('last_active', self::NAMESPACE_INTERNAL);
        $userAgentHash = self::get('user_agent_hash', self::NAMESPACE_INTERNAL);

        if($lastActive && ($lastActive+self::MAX_AGE) < time()) {
            return false;
        }
        self::set('last_active', time(), self::NAMESPACE_INTERNAL);

        $currUserAgentHash = sha1($_SERVER['HTTP_USER_AGENT'] ?? '__NONE__');
        if($userAgentHash && $userAgentHash !== $currUserAgentHash) {
            return false;
        }
        self::set('user_agent_hash', $currUserAgentHash, self::NAMESPACE_INTERNAL);

        return true;
    }

    /**
     * Regenerate session ID
     */
    public static function regenerateId() {
        session_regenerate_id(false);
    }

    /**
     * Destroy session
     */
    public static function destroy() {
        $_SESSION = array();
        session_unset();
        session_destroy();
    }

    /**
     *
     * @param string $key
     * @param string $namespace
     * @return mixed
     */
    public static function get($key, $namespace=self::NAMESPACE_DEFAULT) {
        return $_SESSION[$namespace][$key] ?? null;
    }

    /**
     *
     * @param string $key
     * @param mixed $val
     * @param string $namespace
     * @return boolean
     */
    public static function set($key, $val, $namespace=self::NAMESPACE_DEFAULT) {
        // Ensure we're storing arrays as objects
        if (is_array($val)) {
            $val = (object) $val;
        }

        if($val === null) {
            unset($_SESSION[$namespace][$key]);
        } else {
            $_SESSION[$namespace][$key] = $val;
        }

        return true;
    }

    /**
     * @deprecated in favor of self::set
     * @param string $key
     * @param string $namespace
     */
    public static function remove($key, $namespace=self::NAMESPACE_DEFAULT) {
        self::set($key, null, $namespace);
    }

    /**
     *
     * @param string $namespace
     */
    public static function resetNamespace($namespace) {
        unset($_SESSION[$namespace]);
    }

    /**
     *
     */
    protected static function _iniConfig() {
        $handler = \Flannel\Core\Config::get('session.handler');
        $savepath = \Flannel\Core\Config::get('session.savepath');

        if($handler==='files' && !file_exists($savepath)) {
            mkdir($savepath, 0644, true);
        }

        ini_set('session.save_handler', $handler);
        ini_set('session.save_path', $savepath);
        ini_set('session.cookie_secure', (\Flannel\Core\Config::get('env.https.enforce') ? 'On' : 'Off'));

        // this could effects other apps on the system so only adjust it if needed
        if((int)ini_get('session.gc_maxlifetime') < self::MAX_AGE) {
            ini_set('session.gc_maxlifetime', self::MAX_AGE);
        }
    }

}

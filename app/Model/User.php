<?php

class Model_User extends \FlannelCore\Db\Row {

    const STATUS_DISABLED = 0;
    const STATUS_ENABLED  = 1;

    const PW_HASH_ALGO = PASSWORD_DEFAULT;
    const PW_HASH_COST = 12;

    const PW_RESET_MAX_AGE = SECONDS_PER_HOUR/2;

    const MAX_LOGIN_FAILURES = 6;

    /**
     *
     */
    public function __construct($data=[]) {
        $this->_initDbTable('app', 'user', 'user_id', [
            'user_id' => [
                'update' => false
            ],
            'created_at' => [
                'insert' => 'UTC_TIMESTAMP()',
                'update' => false,
            ],
            'modified_at' => [
                'insert' => 'UTC_TIMESTAMP()',
                'update' => 'UTC_TIMESTAMP()',
            ],
        ]);

        return parent::__construct($data);
    }

    public static function getStatuses() {
        return array(
            'Enabled'                => 1,
            'Disabled'               => 0,
        );
    }

    /*
     * Validate a password
     */
    public function validatePassword($password) {
        $result = password_verify($password, $this->getPassword());
        if ($result) {
            $this->_checkRehash($password);
        }
        return $result;
    }

    /*
     * Automatcally ehashes a password after a successful validation if needed
     */
    protected function _checkRehash($password) {
        if (password_needs_rehash($this->getPassword(), self::PW_HASH_ALGO, array('cost'=>self::PW_HASH_COST))) {
            $this->addData(['password' => self::createPasswordHash($password)])->save();
        }
        return true;
    }

    /**
    * @param string $password
    * @return string
    */
    public static function createPasswordHash($value) {
        return password_hash($value, self::PW_HASH_ALGO, array('cost'=>self::PW_HASH_COST));
    }

    public static function generateTemporaryPassword() {
        return bin2hex(random_bytes(12));
    }

    public static function createAccessKey() {
        return bin2hex(random_bytes(32));
    }

    /*
     * The access token is a JWT containing an access key 
     * to allow for revocation of an access token
     */
    public function generateAccessToken() {
        return \Flannel\Core\JWT::encode([
            'u'  => $this->getUserId(),
            'c'  => time(),
            'ak' => $this->getAccessKey(),
            't'  => 'user',
        ]);
    }

}

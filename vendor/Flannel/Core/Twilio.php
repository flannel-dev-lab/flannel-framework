<?php

namespace Flannel\Core;

use \Twilio\Rest\Client;

\Flannel\Core\Config::required(['env.developer_mode','twilio.sid', 'twilio.token', 'twilio.number']);

/**
 * Static class wrapper for Twilio
 *
 * By default, writes to /{LOG_DIR}/{$channel}.log
 *
 */
class Twilio {

    protected static $_client;

    public function __construct() {
        if (self::$_client == null) {
            self::$_client = new Client(
                \Flannel\Core\Config::get('twilio.sid'),
                \Flannel\Core\Config::get('twilio.token')
            );
        }
    }

    public function sendSms($to, $body) {
        self::$_client->messages->create(
            $to,
            [
                'from' => \Flannel\Core\Config::get('twilio.number'),
                'body' => $body,
            ]
        );
    }

    


}

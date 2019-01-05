<?php

namespace Flannel\Core;

use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;

\Flannel\Core\Config::required(['env.developer_mode','dir.log','alerts.file','alerts.email','alerts.slack']);

/**
 * Static class wrapper for Monolog
 *
 * By default, writes to /{LOG_DIR}/{$channel}.log
 *
 * Use:
 * \Flannel\Core\Monolog::get()->info('Hello');
 * \Flannel\Core\Monolog::get('mailer')->warning('Hello');
 * \Flannel\Core\Monolog::get('braintree')->error('Hello');
 *
 * @see https://seldaek.github.io/monolog/
 */
class Monolog {

    const CHANNEL_DEFAULT = 'default';

    const DEBUG     = Logger::DEBUG;
    const INFO      = Logger::INFO;
    const NOTICE    = Logger::NOTICE;
    const WARNING   = Logger::WARNING;
    const ERROR     = Logger::ERROR;
    const CRITICAL  = Logger::CRITICAL;
    const ALERT     = Logger::ALERT;
    const EMERGENCY = Logger::EMERGENCY;

    /**
     *
     * @param string $channel
     * @return \Monolog\Logger
     */
    public static function get($channel=self::CHANNEL_DEFAULT) {
        if(!\Monolog\Registry::hasLogger($channel)) {
            self::addChannel($channel);
        }
        return \Monolog\Registry::$channel();
    }

    /**
     *
     * @param string $name
     * @param array $handlers of \Monolog\Handler\AbstractProcessingHandler
     */
    public static function addChannel($name, $handlers=null) {
        $logger = new \Monolog\Logger($name);
        \Monolog\Registry::addLogger($logger);

        if($handlers instanceof \Monolog\Handler\AbstractProcessingHandler) {
            $handlers = array($handlers);
        }

        if(is_array($handlers)) {
            foreach($handlers as $handler) {
                $logger->pushHandler($handler);
            }
        }

        if(is_null($handlers)) {

            //file
            if(\Flannel\Core\Config::get('alerts.file')) {
                $logDir = \Flannel\Core\Config::get('dir.log') . DS;
                if(!file_exists($logDir)) {
                    mkdir($logDir, 0644, true);
                }

                //log to file
                $logger->pushHandler(new StreamHandler(
                    $logDir . $name . '.log',
                    \Flannel\Core\Config::get('env.developer_mode') ? Logger::DEBUG : Logger::ERROR
                ));
            }

            //email
            $alertsEmail = \Flannel\Core\Config::get('alerts.email');
            if($alertsEmail) {
                $emailHandler = new \Monolog\Handler\NativeMailerHandler($alertsEmail, 'Error Alert', $alertsEmail, Logger::DEBUG);
                $logger->pushHandler(new \Monolog\Handler\FingersCrossedHandler(
                    $emailHandler,
                    Logger::ERROR
                ));
            }

            //Slack
            $alertsSlack = \Flannel\Core\Config::get('alerts.slack');
            if($alertsSlack) {
                $emailHandler = (new \Monolog\Handler\SlackWebhookHandler($alertsSlack))->setLevel(Logger::DEBUG);
                $logger->pushHandler(new \Monolog\Handler\FingersCrossedHandler(
                    $emailHandler,
                    \Flannel\Core\Config::get('env.developer_mode') ? Logger::NOTICE : Logger::ERROR
                ));
            }
        }
    }

}

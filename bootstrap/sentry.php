<?php

// Load sentry.io if enabled
if (\Flannel\Core\Config::get('sentry.enabled')) {
    $client = new Raven_Client(\Flannel\Core\Config::get('sentry.url'));
    $client->setEnvironment(\Flannel\Core\Config::get('sentry.environment'));
    $error_handler = new Raven_ErrorHandler($client);
    $error_handler->registerExceptionHandler();
    $error_handler->registerErrorHandler();
    $error_handler->registerShutdownFunction();
}

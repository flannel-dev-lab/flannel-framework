<?php

// Load the framework & configuration
require_once( dirname(dirname(__FILE__)) . '/bootstrap.php' );

\Flannel\Core\SQLUpdater::runDatabaseUpdates();

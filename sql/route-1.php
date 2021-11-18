<?php

$sql = "CREATE TABLE `route` (
            `route_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `url` varchar(255) NOT NULL,
            `uuid` varchar(255) NOT NULL,
            `http_status` INT(6) NOT NULL DEFAULT 200,
            `status` TINYINT(3) NOT NULL DEFAULT 0,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY `PK_ROUTE_ID` (`route_id`),
            UNIQUE KEY `UK_URL_HTTP_STATUS` (`url`, `http_status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8";

\Flannel\Core\Db::getLink('app')->query($sql);

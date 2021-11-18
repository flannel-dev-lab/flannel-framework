<?php

$sql = "CREATE TABLE `template` (
            `template_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `uuid` varchar(255) NOT NULL,
            `filename` varchar(255) NOT NULL,
            `status` TINYINT(3) NOT NULL DEFAULT 0,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY `PK_TEMPLATE_ID` (`template_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8";

\Flannel\Core\Db::getLink('app')->query($sql);

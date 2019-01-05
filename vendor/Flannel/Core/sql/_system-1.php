<?php

$sql = "CREATE TABLE `_system` (
            `module_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `module_name` varchar(50) NOT NULL,
            `module_version` mediumint(9) unsigned NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `modified_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`module_id`),
            UNIQUE KEY `UN_MODULE_NAME` (`module_name`),
            KEY `IDX_MODULE_NAME` (`module_name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8";

\Flannel\Core\Db::getLink('app')->query($sql);

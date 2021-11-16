<?php

$sql = "CREATE TABLE `post` (
            `post_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `uuid` varchar(255) NOT NULL,
            `page_uuid` varchar(255) NOT NULL,
            `title` varchar(255) NOT NULL,
            `html` TEXT NOT NULL,
            `template` TEXT NOT NULL,
            `css` TEXT NOT NULL,
            `meta` varchar(255) NOT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `version` varchar(255) NOT NULL DEFAULT '1.0',
            PRIMARY KEY `PK_POST_ID` (`post_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8";

\Flannel\Core\Db::getLink('app')->query($sql);

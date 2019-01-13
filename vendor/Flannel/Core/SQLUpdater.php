<?php

namespace Flannel\Core;

class SQLUpdater {

    public static function runDatabaseUpdates() {
        $result = \Flannel\Core\Db::getLink('app')->query("SHOW TABLES LIKE '_system'", array(), 'assoc');

        $modules = array();
        if ($result) {
            $sql = "SELECT module_name,
                           module_version
                      FROM `_system`";

            $result = \Flannel\Core\Db::getLink('app')->query($sql, array(), 'num', 'none', true);

            foreach ($result as $row) {
                $modules[$row['module_name']] = $row['module_version'];
             }
        }

        $updates = array();
        $rootDirectories = [
            '/vendor/Flannel/Core'
            '/vendor/Flannel/*',
        ];

        $fileSearch = '/sql/*.php';

        $files = [];

        foreach ($rootDirectories as $directory) {
            foreach (glob(ROOT_DIR . $directory, GLOB_ONLYDIR) as $childDirectory) {
                foreach (glob($childDirectory . $fileSearch) as $filename) {
                    $files[] = $filename;
                }        
            }
        }

        // TODO - Move these files into models
        foreach (glob(ROOT_DIR . '/sql/*.php') as $filename) {
            $files[] = $filename;
        }

        // Loop over all found sql files and check if they should be run
        foreach ($files as $filename) {
            list($name, $version) = explode('-', basename($filename, '.php'));

            if (!isset($modules[$name]) || $modules[$name] < $version) {
                $updates[$name][$version] = $filename;
            }
        }

        /*
         * Iterate over the modules and run the updates, while keeping track
         * of the last update.
         */
        foreach ($updates as $moduleName => $versions) {
            // Flip array for natural sorting
            $flipped  = array_flip($versions);
            natsort($flipped);
            $versions = array_flip($flipped);

            foreach ($versions as $version => $filename) {
                require_once($filename);

                $binds = array(
                    'module_name' => $moduleName,
                    'version'     => $version,
                );

                $sql = "REPLACE INTO `_system`
                                 SET module_name    = :module_name,
                                     module_version = :version,
                                     modified_at    = NOW()";

                \Flannel\Core\Db::getLink('app')->query($sql, $binds);
                \Flannel\Core\Db::clearColumnNames();
            }
        }
    }

}

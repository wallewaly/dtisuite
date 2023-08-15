<?php

/**
 * -------------------------------------------------------------------------
 * DTISuite plugin for GLPI
 * -------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of DTISuite.
 *
 * DTISuite is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * DTISuite is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with DTISuite. If not, see <http://www.gnu.org/licenses/>.
 * -------------------------------------------------------------------------
 * @copyright Copyright (C) 2023 by Walison Santos.
 * @license   GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/wallewaly/dtisuie
 * -------------------------------------------------------------------------
 */

/**
 * Plugin install process
 *
 * @return boolean
 */
function plugin_dtisuite_install() {

    global $DB;

   $table = 'glpi_plugin_dtisuite_loans';

   if (!$DB->tableExists($table)) {

      $query = "CREATE TABLE IF NOT EXISTS `$table` (
               `id`           INT(11) NOT NULL auto_increment,
               `user_id`  INT(11) NOT NULL,
               `itemtype_id` INT(11) NOT NULL,
               `item_id` INT(11) NOT NULL,
               `loandate`         TIMESTAMP DEFAULT NULL,
               `devoldate`         TIMESTAMP DEFAULT NULL,
               PRIMARY KEY    (`id`),
               KEY            `user_id`  (`user_id`)
            ) ENGINE=innodb DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
      $DB->queryOrDie($query, $DB->error());
    }

    $table = 'glpi_plugin_dtisuite_imeis';

    if (!$DB->tableExists($table)) {
 
       $query = "CREATE TABLE IF NOT EXISTS `$table` (
                `id`              INT(11) NOT NULL auto_increment,
                `computers_id`    INT(11) DEFAULT NULL,
                `phones_id`       INT(11) DEFAULT NULL,
                `imei_a`          VARCHAR(255) DEFAULT NULL,
                `imei_b`          VARCHAR(255) DEFAULT NULL,
                PRIMARY KEY    (`id`),
                KEY            `computers_id`  (`computers_id`)
             ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
        $DB->queryOrDie($query, $DB->error());
    }

   return true;
}

/**
 * Plugin uninstall process
 *
 * @return boolean
 */
function plugin_dtisuite_uninstall() {
    global $DB;

    $table = 'glpi_plugin_dtisuite_loans';
    
    $DB->queryOrDie("DROP TABLE IF EXISTS `".$table."`", $DB->error());

    $table = 'glpi_plugin_dtisuite_imeis';

    $DB->queryOrDie("DROP TABLE IF EXISTS `".$table."`", $DB->error());

    return true;
}

function plugin_dtisuite_itemimeiupdate_called (CommonDBTM $item) {
    PluginDtisuiteImeiinfo::UpdateImeiInfo($item);
}

function plugin_dtisuite_computerpreItemForm($params){
    if (isset($params['item']) && $params['item'] instanceof CommonDBTM) {
       switch (get_class($params['item'])) {
          case 'Computer':
                PluginDtisuiteImeiinfo::DisplayImei($params['item']);
             break;
 
           case 'Phone':
                PluginDtisuiteImeiinfo::DisplayImei($params['item']);
             break;
       }
    }
 }
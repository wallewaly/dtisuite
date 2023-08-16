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

class PluginDtisuiteLog extends CommonDBTM {

    function RegisterLog($logtype,$type,$equipment,$param1,$param2){

        global $DB;

        $curruser = $_SESSION['glpifriendlyname'];
        $currusername = $_SESSION['glpiname'];
        $curruserid = ($_SESSION['glpiID']);

        if($logtype == 'CDC'){

            $query = "SELECT name
            FROM `glpi_groups`
            WHERE id = {$param1}";

            $result = $DB->queryOrDie($query, $DB->error());
            $first = $result->fetch_assoc();
            
            if(!empty( $first['name'])){
                $oldcdc = $first['name'];
            }else{
                $oldcdc = '';
            };

            $query = "SELECT name
            FROM `glpi_groups`
            WHERE id = {$param2}";

            $result = $DB->queryOrDie($query, $DB->error());
            $first = $result->fetch_assoc();
            if(!empty( $first['name'])){
                $newcdc = $first['name'];
            };

            
            $query = "INSERT INTO `glpi_logs` (`id`, `itemtype`, `items_id`, `itemtype_link`, `linked_action`, `user_name`, `date_mod`, `id_search_option`, `old_value`, `new_value`) 
                        VALUES (NULL, '$type', '$equipment', '', '0', '$curruser ($curruserid)', now(), '71', '$oldcdc ($param1)', '$newcdc ($param2)')
                    ";

            $result = $DB->queryOrDie($query, $DB->error());
        }

        if($logtype == "Status"){

            $query = "INSERT INTO `glpi_logs` (`id`, `itemtype`, `items_id`, `itemtype_link`, `linked_action`, `user_name`, `date_mod`, `id_search_option`, `old_value`, `new_value`) 
                      VALUES (NULL, '$type', '$equipment', '', '0', '$curruser ($curruserid)', now(), '31', '$param1','$param2')
            ";
            
            $result = $DB->queryOrDie($query, $DB->error());
        }

        if($logtype == "Tecnico"){

            $query = "SELECT A.users_id_tech as userid, B.name as username
            FROM `glpi_".$type."s` as A
            JOIN `glpi_users` AS B ON A.users_id_tech = B.id
            WHERE A.id = {$equipment}";

            $result = $DB->queryOrDie($query, $DB->error());
            $first = $result->fetch_assoc();
            if(!empty($first)){
                $oldusername = $first['username'];
                $olduserid = $first['userid'];
            }else{
                $oldusername = ' ';
                $olduserid = '0';
            }

            if($olduserid != $curruserid){

                $query = "INSERT INTO `glpi_logs` (`id`, `itemtype`, `items_id`, `itemtype_link`, `linked_action`, `user_name`, `date_mod`, `id_search_option`, `old_value`, `new_value`) 
                        VALUES (NULL, '$type', '$equipment', '', '0', '$curruser ($curruserid)', now(), '24', '$oldusername ($olduserid)','$currusername ($curruserid)')
                ";
                
                $result = $DB->queryOrDie($query, $DB->error());
            }
        }

        if($logtype == "Usuario"){

            if(empty($param1)){
                $query = "SELECT A.users_id as userid, B.name as username
                FROM `glpi_".$type."s` as A
                JOIN `glpi_users` AS B ON A.users_id = B.id
                WHERE A.id = {$equipment}";

                $result = $DB->queryOrDie($query, $DB->error());
                $first = $result->fetch_assoc();
                if(!empty($first)){
                    $oldusername = $first['username'];
                    $olduserid = $first['userid'];
                }else{
                    $oldusername = ' ';
                    $olduserid = '0';
                }
            }

            if(isset($param2)){
                $query = "SELECT A.name as username
                FROM `glpi_users` AS A
                WHERE A.id = {$param2}";

                $result = $DB->queryOrDie($query, $DB->error());
                $first = $result->fetch_assoc();
                if(!empty($first)){
                    $newusername = $first['username'];
                    $newuserid = $param2;
                }else{
                    $newusername = ' ';
                    $newuserid = '0';
                }
            }

            if($olduserid != $curruserid){

                $query = "INSERT INTO `glpi_logs` (`id`, `itemtype`, `items_id`, `itemtype_link`, `linked_action`, `user_name`, `date_mod`, `id_search_option`, `old_value`, `new_value`) 
                        VALUES (NULL, '$type', '$equipment', '', '0', '$curruser ($curruserid)', now(), '70', '$oldusername ($olduserid)','$newusername ($newuserid)')
                ";
                
                $result = $DB->queryOrDie($query, $DB->error());
            }
        }
    }
}
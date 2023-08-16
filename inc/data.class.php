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

class PluginDtisuiteData extends CommonDBTM {

    function get_equipment_type($itemtype_id){

        if (isset($itemtype_id) && !empty($itemtype_id)){

            switch($itemtype_id){
                case 1:
                    $typename = "computer";
                break;

                case 2:
                    $typename = "phone";
                break;

                case 3:
                    $typename = "monitor";
                break;
            }

        }

        return $typename;

    }

    function get_all_employees(){

        global $DB;

        $query = "SELECT CONCAT (firstname,' ', realname) AS name, id
                    FROM `glpi_users`
                    WHERE firstname is not null 
                    AND realname is not null
                    AND (firstname is not null OR realname is not null)
                    AND (firstname <>'' OR realname <>'')
                    AND is_active = '1'
                    ORDER BY name ASC;";

        $result = $DB->queryOrDie($query, $DB->error());
        $first  = $result->fetch_all();
                
        return json_encode(array('data'=>$first));

    }

    function get_all_available_equips($equipmenttype){

        global $DB;

        $query = "SELECT A.name, A.id
                    FROM `glpi_".$equipmenttype."s` as A
                    JOIN `glpi_states` as B ON A.states_id = B.id
                    WHERE B.name = 'Disponível'
                    ORDER BY name ASC;";

        $result = $DB->queryOrDie($query, $DB->error());
        $first  = $result->fetch_all();

        return json_encode(array('data'=>$first));

    }

    function get_loans_employee($itemtype){

        global $DB;

        $query = "SELECT DISTINCT CONCAT (B.firstname,' ', B.realname) AS name, B.id AS id
                    FROM `glpi_plugin_dtisuite_loans` as A
                    INNER JOIN `glpi_users` AS B ON A.user_id = B.id
                    WHERE B.firstname is not null
                    AND B.realname is not null
                    AND A.itemtype_id = {$itemtype}
                    AND A.devoldate is null
                    ORDER BY name;";

        $result = $DB->queryOrDie($query, $DB->error());
        $first  = $result->fetch_all();
                    
        return json_encode(array('data'=>$first));

    }

    function get_loans_equips($employee,$typeid){


        global $DB;

        $gtdata = new PluginDtisuiteData();
$equipmenttype = $gtdata->get_equipment_type($typeid);


        $query = "SELECT B.name, B.id
                    FROM glpi_plugin_dtisuite_loans as A
                    INNER JOIN `glpi_{$equipmenttype}s` AS B ON A.item_id = B.id
                    WHERE A.user_id = {$employee}
                    AND `A`.itemtype_id = {$typeid}
                    AND A.devoldate is null
                    ORDER BY name ASC;";

        $result = $DB->queryOrDie($query, $DB->error());
        $first  = $result->fetch_all();

        return json_encode(array('data'=>$first));

    }

    function get_loans_all_employees(){

        global $DB;
    
        $query = "SELECT DISTINCT CONCAT (B.firstname,' ', B.realname) as name, B.id as id
                    FROM `glpi_plugin_dtisuite_loans` as A
                    INNER JOIN `glpi_users` AS B ON A.user_id = B.id
                    WHERE B.firstname is not null
                    AND B.realname is not null
                    AND A.devoldate is null
                    ORDER BY name;";

        $result = $DB->queryOrDie($query, $DB->error());
        $first  = $result->fetch_all();

        return json_encode(array('data'=>$first));

    }

    function get_employee_details($employee){

        global $DB;
    
        $query = "SELECT DISTINCT CONCAT (firstname,' ', realname) as name, id
                    FROM `glpi_users`
                    WHERE id = ".$employee."
                    AND firstname is not null
                    AND realname is not null
                    ORDER BY name;";

        $result = $DB->queryOrDie($query, $DB->error());
        $first  = $result->fetch_assoc();

        return $first;

    }

    function get_equip_details($equipment,$typeid){

        global $DB;

        $gtdata = new PluginDtisuiteData();
$equipmenttype = $gtdata->get_equipment_type($typeid);

        $query = "SELECT name, id
                    FROM `glpi_".$equipmenttype."s`
                    WHERE id = ".$equipment."
                    ORDER BY name ASC;";

        $result = $DB->queryOrDie($query, $DB->error());

        return $result;

    }

    function set_new_loan_data($employee,$equipment,$equipmenttype){

        global $DB;

        $query = "INSERT INTO `glpi_plugin_dtisuite_loans` (`id`, `user_id`, `itemtype_id`, `item_id`, `loandate`) 
                    VALUES (NULL, '{$employee}', '{$equipmenttype}', '{$equipment}', now());";
        
        $result = $DB->queryOrDie($query, $DB->error());
        
    }

    function set_partial_devol_data($employee,$equipment,$typeid){

        global $DB;


        $gtdata = new PluginDtisuiteData();
$equipmenttype = $gtdata->get_equipment_type($typeid);

        $query = "UPDATE glpi_plugin_dtisuite_loans
                    SET devoldate = now()
                    WHERE user_id = {$employee}
                    AND itemtype_id = {$typeid}
                    AND item_id = {$equipment}";
        
        $result = $DB->queryOrDie($query, $DB->error());

        return $result;
        
    }

    function get_full_devol_data($employee){

        global $DB;

        $query = "SELECT A.itemtype_id, A.item_id
                    FROM `glpi_plugin_dtisuite_loans` as A
                    WHERE A.user_id = ".$employee."
                    AND A.devoldate is null;";

        $result = $DB->queryOrDie($query, $DB->error());

        return $result;



    }

    function check_loan_duplicate($employee,$equipment,$typeid){

        global $DB;


        $gtdata = new PluginDtisuiteData();
$equipmenttype = $gtdata->get_equipment_type($typeid);



        $query = "SELECT COUNT(*) as total
                    FROM`glpi_plugin_dtisuite_loans`
                    WHERE user_id = {$employee}
                    AND itemtype_id = {$typeid}
                    AND item_id = {$equipment}
                    AND devoldate IS NULL;";
        
        $first = $DB->queryOrDie($query, $DB->error());
        
        if(!empty($first)){
            $result = $first->fetch_assoc();
            $total = $result['total'];
        }else{
            $total = '0';
        }

        return $total;
        
    }

    function check_equip_association($employee,$equipment,$typeid){

        global $DB;

        $gtdata = new PluginDtisuiteData();
$equipmenttype = $gtdata->get_equipment_type($typeid);

        $query = "SELECT COUNT(*) as total
                    FROM`glpi_plugin_dtisuite_loans`
                    WHERE user_id <> {$employee}
                    AND itemtype_id = {$typeid}
                    AND item_id = {$equipment}
                    AND devoldate IS NULL;";
        
        $first = $DB->queryOrDie($query, $DB->error());

        if(!empty($first)){
            $result = $first->fetch_assoc();
            $total = $result['total'];
        }else{
            $total = '0';
        }


        return $total;
        
    }

    function set_equip_state($usersid,$equipment,$typeid,$action){

        global $DB;

        $logging = new PluginDtisuiteLog();
        $gtdata = new PluginDtisuiteData();

        $equipmenttype = $gtdata->get_equipment_type($typeid);

        $query = "SELECT groups_id as id
        FROM glpi_users
        WHERE id = {$usersid}";

        $result = $DB->queryOrDie($query, $DB->error());
        $first = $result->fetch_assoc();
        $groupsid = $first['id'];

        if($action == "loan"){

            $state = "Ativo";
            $setuser = $usersid;
            $type = ucfirst($equipmenttype);

            $logging->RegisterLog('Status',$type,$equipment,'Disponível (1)', 'Ativo (2)');

            $query = "SELECT groups_id
            FROM `glpi_".$equipmenttype."s`
            WHERE id = {$equipment}";

            $result = $DB->queryOrDie($query, $DB->error());
            $first = $result->fetch_assoc();
            $old_cdc = $first['groups_id'];

            if($old_cdc != $groupsid){

                $logging->RegisterLog('CDC',$type,$equipment,$old_cdc,$groupsid);

            }

            $logging->RegisterLog('Usuario',$type,$equipment,'', $setuser);


        } 
        else if($action == "devol"){

            $state = "Disponível";
            $setuser = "0";

            $curruser = $_SESSION['glpifriendlyname'];
            $curruserid = ($_SESSION['glpiID']);
            $type = ucfirst($equipmenttype);

            $logging->RegisterLog('Status',$type,$equipment,'Ativo (2)', 'Disponível (1)');
            $logging->RegisterLog('Usuario',$type,$equipment,'', '0');


        }

        $logging->RegisterLog('Tecnico',$equipmenttype,$equipment,'','');
        
        $query = "SELECT id
        FROM glpi_states
        WHERE name = '$state';";

        $result = $DB->queryOrDie($query, $DB->error());
        $first = $result->fetch_assoc();
        $stateid = $first['id'];

        $query = "UPDATE `glpi_".$equipmenttype."s`
                    SET states_id = {$stateid}, users_id = {$setuser}, groups_id = {$groupsid}, users_id_tech = {$_SESSION['glpiID']}
                    WHERE id = {$equipment}";
        
        $result = $DB->queryOrDie($query, $DB->error());

        return $result;
        
    }

    function get_loan_id($employee, $itemtype_id, $itemid){

        global $DB;

        $query = "SELECT id
                  FROM `glpi_plugin_dtisuite_loans`
                  WHERE user_id = {$employee}
                  AND itemtype_id = {$itemtype_id}
                  AND item_id = {$itemid}
                  ORDER BY id DESC
                  LIMIT 1;";
        
        $result = $DB->queryOrDie($query, $DB->error());
        $first = $result->fetch_assoc();
        $loanid = $first['id'];


        return $loanid;


    }
}
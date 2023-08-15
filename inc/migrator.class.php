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

class PluginDtisuiteMigrator extends CommonDBTM {
    
    function GetAllComputerLoans(){
        
        global $DB;

        $query = "SELECT A.id, A.user as user, A.cpf as cpf, A.computer_id, B.id as userid, C.id as groupid
        FROM glpi_plugin_helpdesk_employees AS A
        JOIN glpi_users AS B ON A.user = B.name
        JOIN glpi_groups AS C on A.cdc = C.name
        WHERE computer_id is not NULL
        AND computer_id <> '0'
        AND user is not null;";

        $result = $DB->query($query);

        while ($loan = $result->fetch_assoc()){

            //Criar query para inserir user no grupo select na table glpi_groups_users

            $mgquery = "UPDATE glpi_users
                        SET groups_id = '".$loan['groupid']."',
                        registration_number = '".$loan['cpf']."'
                        WHERE id = '".$loan['userid']."';
                        ";

            $mgresult = $DB->query($mgquery);

            echo "CPF: ".$loan['cpf']." - User: ".$loan['userid']." - ".$loan['user']." - Grupo: ".$loan['groupid']." <br />";
            

        }
    }

}

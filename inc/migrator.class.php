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

        $query = "SELECT A.id, A.user as user, A.cpf as cpf, A.computer_id, B.id as userid, C.id as groupid, A.computer_id, A.phone_id, A.monitor_id
        FROM glpi_plugin_helpdesk_employees AS A
        JOIN glpi_users AS B ON A.user = B.name
        JOIN glpi_groups AS C on A.cdc = C.name
        WHERE computer_id is not NULL
        AND computer_id <> '0'
        AND user is not null;";

        $result = $DB->queryOrDie($query, $DB->error());

        echo "<h2>Migrating user info</h2> <br />";

        while ($loan = $result->fetch_assoc()){

            //Criar query para inserir user no grupo select na table glpi_groups_users

            $mgquery = "UPDATE glpi_users
                        SET groups_id = '".$loan['groupid']."',
                        registration_number = '".$loan['cpf']."'
                        WHERE id = '".$loan['userid']."';
                        ";

            $mgresult = $DB->query($mgquery);

            echo "<strong>Migrando dados de usuário</strong> - User: ".$loan['userid']." - ".$loan['user']." - Grupo: ".$loan['groupid']."";

            $mgquery = "SELECT id 
                        FROM `glpi_groups_users`
                        WHERE users_id ='".$loan['userid']."'
                        AND groups_id = '".$loan['groupid']."';";

            $mgresult = $DB->query($mgquery);
            
            if(empty($mgresult)){

                $mgquery = "INSERT INTO `glpi_groups_users` (`id`, `users_id`, `groups_id`, `is_dynamic`, `is_manager`, `is_userdelegate`) VALUES (NULL, ".$loan['userid'].", ".$loan['groupid'].", '0', '0', '0');";

                $mgresult = $DB->query($mgquery);
    
            } else{
                echo "<br /><strong>Migrando dados de grupo</strong> - User " . $loan['userid'] . " já está no grupo " . $loan['groupid'] . "<br />";
            }
            
            echo "<strong>Migrando dispositivos<br /></strong>";

            if(isset($loan['computer_id']) && $loan['computer_id'] != '0'){

                $mgquery0 = "SELECT id
                            FROM  `glpi_plugin_dtisuite_loans`
                            WHERE user_id = ".$loan['userid']."
                            AND itemtype_id = '1'
                            AND item_id = ".$loan['computer_id'].";";

                $mgresult0 = $DB->query($mgquery0);
                $mgdata = $mgresult0->fetch_assoc();

                if(empty($mgdata)){

                    $mgquery1 = "INSERT INTO `glpi_plugin_dtisuite_loans` (`id`, `user_id`, `itemtype_id`, `item_id`, `loandate`, `devoldate`) 
                                VALUES (NULL, '".$loan['userid']."', '1', '".$loan['computer_id']."', '2008-01-01', NULL);
                                ";

                    $mgresult1 = $DB->query($mgquery1);

                    $mgquery2 = "UPDATE `glpi_computers`
                                 SET users_id = ".$loan['userid'].",
                                     groups_id = ".$loan['groupid']."
                                 WHERE id = ".$loan['computer_id'].";
                                 
                                ";

                    $mgresult2 = $DB->query($mgquery2);

                    echo "&bull; Adicionando empréstimo do computador " . $loan['computer_id'] . "<br />";

                } 
                
                else{
                    echo "Erro ao importar dados do computador " . $loan['computer_id'] . "<br />";
                }    
            }

            if(isset($loan['phone_id']) && $loan['phone_id'] != '0'){


                $mgquery0 = "SELECT id
                            FROM  `glpi_plugin_dtisuite_loans`
                            WHERE user_id = ".$loan['userid']."
                            AND itemtype_id = '2'                            
                            AND item_id = ".$loan['phone_id'].";";

                $mgresult0 = $DB->query($mgquery0);
                $mgdata = $mgresult0->fetch_assoc();

                if(empty($mgdata)){

                    $mgquery1 = "INSERT INTO `glpi_plugin_dtisuite_loans` (`id`, `user_id`, `itemtype_id`, `item_id`, `loandate`, `devoldate`) 
                                VALUES (NULL, '".$loan['userid']."', '2', '".$loan['phone_id']."', '2008-01-01', NULL);
                                ";

                    $mgresult1 = $DB->query($mgquery1);

                    $mgquery2 = "UPDATE `glpi_phones`
                                 SET users_id = ".$loan['userid'].",
                                     groups_id = ".$loan['groupid'].";
                                 WHERE id = ".$loan['phone_id'].";
                                ";

                    $mgresult2 = $DB->query($mgquery2);

                    echo "&bull; Adicionando empréstimo do telefone " . $loan['phone_id'] . "<br />";


                }

                else{
                    echo "Erro ao importar dados do telefone " . $loan['phone_id'] . "<br />";
                }
            }

            if(isset($loan['monitor_id']) && $loan['monitor_id'] != '0'){

                $mgquery0 = "SELECT id
                            FROM  `glpi_plugin_dtisuite_loans`
                            WHERE user_id = ".$loan['userid']."
                            AND itemtype_id = '3'
                            AND item_id = ".$loan['monitor_id'].";";

                $mgresult0 = $DB->query($mgquery0);
                $mgdata = $mgresult0->fetch_assoc();
                
                if(empty($mgdata)){

                    $mgquery1 = "INSERT INTO `glpi_plugin_dtisuite_loans` (`id`, `user_id`, `itemtype_id`, `item_id`, `loandate`, `devoldate`) 
                                VALUES (NULL, '".$loan['userid']."', '3', '".$loan['monitor_id']."', '2008-01-01', NULL);
                                ";

                    $mgresult1 = $DB->query($mgquery1);

                    $mgquery2 = "UPDATE `glpi_monitors`
                                 SET users_id = ".$loan['userid'].",
                                     groups_id = ".$loan['groupid']."
                                  WHERE id = ".$loan['monitor_id'].";
                                ";

                    $mgresult2 = $DB->query($mgquery2);

                    echo "&bull; Adicionando empréstimo do monitor " . $loan['monitor_id'] . "<br />";


                }
                else{
                    echo "Erro ao importar dados do monitor " . $loan['monitor'] . "<br />";
                }

            }

            echo "<br />";
        }

        echo "<h2>End of user info</h2> <br />";

        $query = "UPDATE `glpi_logs`
                  SET `id_search_option` = '71'
                  WHERE `id_search_option` = '49'
                  ";
        $result = $DB->queryOrDie($query, $DB->error());
    

    }

}

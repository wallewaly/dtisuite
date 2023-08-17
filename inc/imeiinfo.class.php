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
 
class PluginDtisuiteImeiinfo extends CommonDBTM {

    static function UpdateImeiInfo($item){
        global $DB;
      
        $compid = $_POST['id'];
        $imeia = $_POST['imeia'];
        $imeib = $_POST['imeib'];

        switch (get_class($item)) {
            case 'Computer':
                $typeequipment = 'computer';
            break;

            case 'Phone':
                $typeequipment = 'phone';
            break;

        }

        if ($item::getType() !== Monitor::getType() && $item::getType() === Phone::getType() OR $type == 8 OR $type == null){
        
        $query ="SELECT id
        FROM `glpi_plugin_dtisuite_imeis`
        WHERE ".$typeequipment."s_id = '$compid'
        ";

        $result = $DB->query($query);
        $first  = $result->fetch_assoc();
        $check = $first['id'];

        }
        
        if (isset($_POST['update'])){

            if (isset($imeia) & !empty($imeia) & $imeia == $imeib) {

                Session::addMessageAfterRedirect('IMEI A e IMEI B não podem ser iguais');
            
            } else {
            
                if (isset($check) && isset($imeib)){

                    $DB->update(
                        'glpi_plugin_dtisuite_imeis', [
                            'imei_b' => "$imeib"
                        ], [
                            "{$typeequipment}s_id" => "$compid"
                        ]
                        );

                
                }
                if (isset($check) && isset($imeia)){
    
                    $DB->update(
                        'glpi_plugin_dtisuite_imeis', [
                            'imei_a' => "$imeia"
                        ], [
                            "{$typeequipment}s_id" => "$compid"
                        ]
                        );

                }
                if (!isset($check)){
    
                    $DB->insert(
                        'glpi_plugin_dtisuite_imeis', [
                            'id' => '0',
                            "{$typeequipment}s_id" => "$compid",
                            'imei_a' => "$imeia",
                            'imei_b'=>"$imeib"
                        ]
                    );
                } 
            }            
        }
    }
    
    static function DisplayImei($item){
        global $DB;

        switch (get_class($item)) {
            case 'Computer':
                $typeequipment = 'computer';
            break;

            case 'Phone':
                $typeequipment = 'phone';
            break;

        }

            $query ="SELECT ".$typeequipment."types_id as typeid, b.name as typename
                    FROM `glpi_".$typeequipment."s` AS a
                    JOIN glpi_".$typeequipment."types AS b ON a.".$typeequipment."types_id = b.id
                    WHERE a.id = '".$_GET['id']."'
            ";

            $result=$DB->queryOrDie($query, $DB->error());
            $first=$result->fetch_assoc();
            $typeid = $first['typeid'];
            $type=$first['typename'];
            

        
        if ($item::getType() === Phone::getType() || strtoupper($type) == "CELULAR" || strtoupper($type) == "TABLET" || strtoupper($type) == "MODEM"){
        
        $query ="SELECT *
                 FROM `glpi_plugin_dtisuite_imeis`
                 WHERE ".$typeequipment."s_id = '".$_GET['id']."'
        ";

        $result=$DB->queryOrDie($query, $DB->error());
        $first=$result->fetch_assoc();
        if(isset($first['imei_a'])){
            $imeia = $first['imei_a'];
        }
        else{
            $imeia = "";
        }
        if(isset($first['imei_b'])){
            $imeib = $first['imei_b'];
        }
        else{
            $imeib = "";
        }


            echo '<div class="card-body d-flex flex-wrap">';
            echo '  <div class="col-12 col-xxl-12 flex-column">';
            echo '      <div class="d-flex flex-row flex-wrap flex-xl-nowrap">';
            echo '          <div class="row flex-row align-items-start flex-grow-1">';
            echo '              <div class="row flex-row">';

            echo '                  <div class="form-field row col-12 col-sm-6  mb-2">';
	        echo '                      <label class="col-form-label col-xxl-5 text-xxl-end" >IMEI A</label>';
            echo '                      <div class="col-xxl-7  field-container">';
		    echo '                          <input type="text" id="imeia" class="form-control " name="imeia" value="'.$imeia.'" required autocomplete="off">';
            echo '	                    </div>';
            echo '                  </div>';

            echo '                  <div class="form-field row col-12 col-sm-6  mb-2">';
	        echo '                      <label class="col-form-label col-xxl-5 text-xxl-end" >IMEI B</label>';
            echo '                      <div class="col-xxl-7  field-container">';
		    echo '                          <input type="text" id="imeib" class="form-control " name="imeib" value="'.$imeib.'" autocomplete="off">';
            echo '	                    </div>';
            echo '                  </div>';

            echo '              </div>';
            echo '          </div>';
            echo '      </div>';
            echo '  </div>';
            echo '</div>';
        }
    }      
}
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

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}
 
class PluginDtisuiteUserinfo extends CommonDBTM {

    static function UpdateUserInfo($item){
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
            if(isset($first['id']) && !empty($first['id'])){

                $check = $first['id'];
            }

            else{

                $check = 'A113';

            }

        }
        
        if (isset($_POST['update'])){

            if (isset($imeia) && !empty($imeia) && !empty($imeib) && $imeia == $imeib) {

                Session::addMessageAfterRedirect('IMEI A e IMEI B não podem ser iguais');
            
            } else {
            
                if (isset($check) && $check != "A113" && isset($imeib)){

                    $DB->update(
                        'glpi_plugin_dtisuite_imeis', [
                            'imei_b' => "$imeib"
                        ], [
                            "{$typeequipment}s_id" => "$compid"
                        ]
                        );
                
                }
                if (isset($check) && $check != "A113" && isset($imeia) && !empty($imeia)){
    
                    $DB->update(
                        'glpi_plugin_dtisuite_imeis', [
                            'imei_a' => "$imeia"
                        ], [
                            "{$typeequipment}s_id" => "$compid"
                        ]
                        );
                }
                if (!isset($check) || $check = "A113"){
    
                    $DB->insert(
                        'glpi_plugin_additionaldata_imeis', [
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

    function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {

        if ($item->getType() == 'User') {
            
                return __('Dados do colaborador', 'Dados do colaborador');
            
            return '';
        }
        return '';
    }
    
    
     public static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0){
        
    
        echo'<div class="card m-n2 border-0 shadow-none">
                                <div class="card-header">
                                    <div class="ribbon ribbon-bookmark ribbon-top ribbon-start bg-blue s-1">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <h4 class="card-title ps-4">
                                        Dados do colaborador
                                    </h4>
                                </div>';

            echo '<div class="card-body d-flex flex-wrap">';
            echo '  <div class="col-12 col-xxl-12 flex-column">';
            echo '      <div class="d-flex flex-row flex-wrap flex-xl-nowrap">';
            echo '          <div class="row flex-row align-items-start flex-grow-1">';
            echo '              <div class="row flex-row">';

            echo '                  <div class="form-field row col-12 col-sm-6  mb-2">';
	        echo '                      <label class="col-form-label col-xxl-5 text-xxl-end" >Ticket de Onboarding</label>';
            echo '                      <div class="col-xxl-7  field-container">';
		    echo '                          <input type="text" class="form-control " name="ticket" id="ticket" value="">';
            echo '	                    </div>';
            echo '                  </div>';

            echo '                  <div class="form-field row col-12 col-sm-6  mb-2">';
	        echo '                      <label class="col-form-label col-xxl-5 text-xxl-end" >CPF</label>';
            echo '                      <div class="col-xxl-7  field-container">';
		    echo '                          <input type="text" class="form-control " name="cpf" id="cpf" value="" required>';
            echo '	                    </div>';
            echo '                  </div>';

            echo '                  <div class="form-field row col-12 col-sm-6  mb-2">';
	        echo '                      <label class="col-form-label col-xxl-5 text-xxl-end" >Razão social</label>';
            echo '                      <div class="col-xxl-7  field-container">';
		    echo '                          <input type="text" class="form-control " name="razaosocial" id="razaosocial" value="">';
            echo '	                    </div>';
            echo '                  </div>';

            echo '                  <div class="form-field row col-12 col-sm-6  mb-2">';
	        echo '                      <label class="col-form-label col-xxl-5 text-xxl-end" >CNPJ</label>';
            echo '                      <div class="col-xxl-7  field-container">';
		    echo '                          <input type="text" class="form-control " name="cnpj" id="cnpj" value="">';
            echo '	                    </div>';
            echo '                  </div>';

            echo '                  <div class="form-field row col-12 col-sm-6  mb-2">';
	        echo '                      <label class="col-form-label col-xxl-5 text-xxl-end" >RDA</label>';
            echo '                      <div class="col-xxl-7  field-container">';
		    echo '                          <input type="text" class="form-control " name="rda" id="rda" value="">';
            echo '	                    </div>';
            echo '                  </div>';

            echo '              </div>';
            echo '          </div>';
            echo '      </div>';
            echo'  <button class="btn btn-primary me-2 float-end mt-sm-3" name="update" id="update">
                    <i class="far fa-save"></i>
                    <span>Salvar</span>&nbsp;
                  </button>';
            echo '  </div>';
            echo '</div>';
            
    }      
}
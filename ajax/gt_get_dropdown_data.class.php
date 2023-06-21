<?php

require '../../../inc/includes.php';

header("Content-Type: text/json; charset=UTF-8");
Html::header_nocache();
Session::checkCentralAccess();

global $DB;

$gtdata = new PluginDtisuiteData();

    if (isset($_POST["tipoItem"]) && !empty($_POST["tipoItem"])){

        //TODO convert equipment types translation into a table record managed on web interface

        switch($_POST["tipoItem"]){
            case 1:
                $equipmenttype = "computer";
            break;

            case 2:
                $equipmenttype = "phone";
            break;

            case 3:
                $equipmenttype = "monitor";
            break;
        }

    }

    if(isset($_POST['tipoTermo'])){

        switch($_POST['tipoTermo']){

            case 1:

                if(isset($_POST['dropdown']) &&!empty($_POST['dropdown'])){

                    switch ($_POST['dropdown']){

                        case "employee":

                            echo $gtdata->get_all_employees();
                            
                            break;


                        case "equip":

                            echo $gtdata->get_all_available_equips($equipmenttype);

                            break;

                    }

                }

            break;

            case 2:

                if(isset($_POST['tipoDevol']) &&!empty($_POST['tipoDevol']) && $_POST['tipoDevol'] == "1"){
                    
                    if(isset($_POST['dropdown']) &&!empty($_POST['dropdown'])){

                        switch ($_POST['dropdown']){

                            case "employee":

                                            echo $gtdata->get_loans_employee($_POST['tipoItem']);
                                
                                break;

                            case "equip":
                                
                                            echo $gtdata->get_loans_equips($_POST['employee'],$_POST['tipoItem']);
                      
                                break;
    
                        }

                    } 

                } else if(isset($_POST['tipoDevol']) &&!empty($_POST['tipoDevol']) && $_POST['tipoDevol'] == "2"){
                    
                    echo $gtdata->get_loans_all_employees();

                }

            break;
        }
    }
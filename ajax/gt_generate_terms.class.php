<?php

require '../../../inc/includes.php';

header("Content-Type: text/json; charset=UTF-8");
Html::header_nocache();
Session::checkCentralAccess();

global $DB;

if(isset($_POST['tipoTermo']) && !empty($_POST['tipoTermo'])){

    $gtdata = new PluginDtisuiteData();

    switch ($_POST['tipoTermo']) {
        case '1':

            $TipoTermo = "Empréstimo";


            $resultemployee = $gtdata->get_employee_details($_POST['employee']);
            $employee = $resultemployee['name'];
            $employeeid = $resultemployee['id'];


            $resultequip = $gtdata->get_equip_details($_POST['equip'],$_POST['tipoItem']);

            $multiloanavoid = $gtdata->check_equip_association($_POST['employee'],$_POST['equip'],$_POST['tipoItem']);
            $duplicateavoid = $gtdata->check_loan_duplicate($_POST['employee'],$_POST['equip'],$_POST['tipoItem']);

            if($duplicateavoid == '0' && $multiloanavoid == '0'){

                $gtdata->set_new_loan_data($_POST['employee'],$_POST['equip'],$_POST['tipoItem']);
                $gtdata->set_equip_state($_POST['employee'],$_POST['equip'],$_POST['tipoItem'],"loan");
                $NmTpTermo = "$TipoTermo";
                show_loan_data($resultequip,$employeeid,$employee,$NmTpTermo);

            }
            else if($duplicateavoid == '0' && $multiloanavoid == '1'){
                
                echo"<script> alert('O equipamento não pode ser emprestado, pois, está associado a um empréstimo ativo para outro colaborador.<br /><br /> É necessário gerar um termo de devolução do empréstimo atual para que você possa emprestá-lo novamente.'); </script>";

            } 
            else if($duplicateavoid == '1' && $multiloanavoid == '0'){
                
                echo"<script> alert('Colaborador já possui um empréstimo para este equipamento'); </script>";

                $resultequip = $gtdata->get_equip_details($_POST['equip'],$_POST['tipoItem']);
                $TipoTermo = "Reimpressão";
                $NmTpTermo = "$TipoTermo";
                show_loan_data($resultequip,$employeeid,$employee,$NmTpTermo);

            }
            


            break;
        
        case '2':
            $TipoTermo = "Devolução";

            if(isset($_POST['tipoDevol']) && !empty($_POST['tipoDevol'])){

                switch($_POST['tipoDevol']){
                    case '1':
                        $TipoDevol = 'Parcial';

                        $resultemployee = $gtdata->get_employee_details($_POST['employee']);
                        $employee = $resultemployee['name'];
                        $employeeid = $resultemployee['id'];


                        $resultequip = $gtdata->get_equip_details($_POST['equip'],$_POST['tipoItem']);
                        
                        $gtdata->set_partial_devol_data($_POST['employee'],$_POST['equip'],$_POST['tipoItem']);
                        $gtdata->set_equip_state($_POST['employee'],$_POST['equip'],$_POST['tipoItem'],"devol");
                        $NmTpTermo = "$TipoTermo $TipoDevol";


                        show_loan_data($resultequip,$employeeid,$employee,$NmTpTermo);

                        
                        break;
                    
                    case '2':
                        $TipoDevol = 'Total';

                        $resultemployee = $gtdata->get_employee_details($_POST['employee']);
                        $employee = $resultemployee['name'];
                        $employeeid = $resultemployee['id'];


                        $resultequip = $gtdata->get_full_devol_data($_POST['employee']);
                        $NmTpTermo = "$TipoTermo $TipoDevol";


                        show_loan_data($resultequip,$employeeid,$employee,$NmTpTermo);

                                                
                        break;
                }


            }
            break;

    }

}
function show_loan_data($resultequip,$employeeid,$employee,$NmTpTermo){

    $gtdata = new PluginDtisuiteData();

    echo '
        <div class="card card-sm mt-0 search-card w-100">
            <div class="table-responsive-lg">
                <table class="search-results table card-table table-hover table-striped" id="search_1673291691">
                    <thead>
                        <tr>
                            <th>
                                Colaborador
                            </th>
                            <th>
                                Tipo
                            </th>
                            <th>
                                Equipamento
                            </th>
                            <th>
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody>';

                    while ($row = $resultequip->fetch_assoc()){
                        
                        if(isset($NmTpTermo) && $NmTpTermo == "Devolução Total"){

                            $resultequip2 = $gtdata->get_equip_details($row['item_id'],$row['itemtype_id'])->fetch_assoc();
                            $equip = $resultequip2['name'];
                            $equipid = $resultequip2['id'];

                            $gtdata->set_partial_devol_data($employeeid,$equipid,$row['itemtype_id']);
                            $gtdata->set_equip_state($_POST['employee'],$equipid,$row['itemtype_id'],"devol");

                            $equipmenttype = $gtdata->get_equipment_type($row['itemtype_id']);

                            $loanid = $gtdata->get_loan_id($employeeid,$row['itemtype_id'],$row['item_id']);



                        } else{

                            $equipmenttype = $gtdata->get_equipment_type($_POST['tipoItem']);
                            $equip = $row['name'];
                            $equipid = $row['id'];

                            $loanid = $gtdata->get_loan_id($employeeid,$_POST['tipoItem'],$equipid);
                        }

                        echo '

                                            <tr>
                                                <td>
                                                    <a href="'.$_SESSION['glpiroot'].'/front/user.form.php?id='.$_POST['employee'].'" target="_blank">
                                                        <strong>'.$employee.'</strong>
                                                    </a>
                                                </td>
                                                <td>
                                                    '.$NmTpTermo.'
                                                </td>
                                                <td>
                                                    <a href="'.$_SESSION['glpiroot'].'\/front/'.$equipmenttype.'.form.php?id='.$equipid.'" target="_blank">
                                                        <strong>'.$equip.'</strong>
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="'.$_SESSION['glpiroot'].'/plugins/dtisuite/front/geratermo.php?loanid='.$loanid.'&view" target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    &nbsp;
                                                    <a href="'.$_SESSION['glpiroot'].'/plugins/dtisuite/front/geratermo.php?loanid='.$loanid.'&download" target="_blank">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    &nbsp;
                                                </td>
                                            </tr>';
                    }
}
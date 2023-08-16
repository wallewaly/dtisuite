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

include ("../../../inc/includes.php");


Html::header(__("Report de troca de CDC", "helpdesk"), $_SERVER['PHP_SELF'],
            "management", "PluginDtisuiteMenureportcdc", "PluginDtisuiteContainerReport");

Session::checkRight('plugin_dtisuite_gen_terms', READ);


global $DB;

  if(!isset($_GET['dateStart'])){
      $dateStart = date("Y-m-d H:i:s");
  }
  else{
    $dateStart = $_GET['dateStart'];
  }

  if(!isset($_GET['dateEnd'])){
    $dateEnd = date("Y-m-d H:i:s");
  } 
  else{
    $dateEnd = $_GET['dateEnd'];
  }

$query =
 "
  SELECT DISTINCT
    c.otherserial,
    c.name,
    s.name as status,
    m.name as fabricante,
    i.order_number as nf_hw,
    i.order_date,
    cast(i.value as decimal(20,2)) as value,
    (cast(i.value as decimal(20,2))/60) as dep_mensal,
    l.old_value as old_cdc,
    l.new_value as new_cdc,
    l.date_mod as data_modificacao
  FROM glpi_computers as c
    left join glpi_states as s on s.id = c.states_id
    left join glpi_manufacturers as m on m.id = c.manufacturers_id
    left join glpi_infocoms as i on i.immo_number = c.otherserial
    join glpi_logs as l on l.items_id = c.id and  l.id_search_option = 71
  WHERE l.date_mod >='".$dateStart."'  and l.date_mod <= '".$dateEnd."'
  order by data_modificacao desc ;";

$result = $DB->query($query);

echo
'
<div class="page-wrapper mb-0">
    <div class="page-body container-fluid">
        <main role="main" id="page" class="legacy">
            <div class="termo ">
                <form name="termo_form" method="GET">
                    <div id="mainformtable">
                        <div class="card m-n2 border-0 shadow-none">
                            <div class="card-header">
                                <div class="ribbon ribbon-bookmark ribbon-top ribbon-start bg-blue s-1">
                                    <i class="fas fa-file-excel"></i>
                                </div>
                                <h4 class="card-title ps-4">
                                    Relatório de CDC
                                </h4>
                            </div>
                        

                        <div class="card-body d-flex flex-wrap">
                            <div class="col-12 col-xxl-12 flex-column">
                                <div class="d-flex flex-row flex-wrap flex-xl-nowrap">
                                    <div class="row flex-row align-items-start flex-grow-1">
                                        <div class="row flex-row">';

 echo '
 <form id="reportform" name = "reportform" action="./reportcdc.form.php">
  <div id="filterReport">
    <table class="tab_cadre_fixe noExl" id="table">
      <tbody>
        <tr class="tab_bg_1">
          <td>
            <table class="tab_format" id="tableFilterReport">
              <tbody>
                <tr class="normalcriteria headerRow" id="rowFilterReport">
                  <td class="left" width="45%">
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <label for="">Data inicial:</label>
                    <input type="date" value="'.$dateStart.'" name="dateStart">
                  </td>
                  <td class="left">
                    <div id="SearchSpanComputer0">
                      <table>
                        <tbody>
                          <tr class="">
                            <td>
                            <label for="">Data final:</label>
                            <input type="date" value="'.$dateEnd.'" name="dateEnd">
                            </td>
                            <td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
          <td width="150px"><table width="100%">
            <tbody>
              <tr class="">
                <td width="80" class="center">
                  <input type="submit" name="search" value="Pesquisar" class="submit">
                </td>
                  <td class="no-wrap">
                </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
  </div>';

if (isset($_GET['search']) && !empty(isset($_GET['search']))) {
echo   '
<div id="page">
  <div class="center">
    <table class="tab_cadrehov" style="text-align: center;">
      <tbody>
        <tr>
          <th colspan="10">Relatório de Troca do CDC</th>
          
         
          <th>
              <a aling ="center"; href="./genexcel.php?dateStart='.$dateStart.'&dateEnd='.$dateEnd.'&search=Pesquisar" >
                  <i class="fas fa-file-excel"></i>
              </a> 
          
          </th>
        </tr>
        <tr>
          <th>Patrimônio</th>
          <th>Descrição</th>
          <th>Tipo</th>
          <th>Fabricante</th>
          <th>NF HW</th>
          <th>Data NF</th>
          <th>Valor Unitário</th>
          <th>Despesa Mensal</th>
          <th>CDC</th>
          <th>CDC Novo</th>
          <th>Data Alteração</th>
        </tr>';

while ($row = $result->fetch_assoc()) {


    if(isset($row["otherserial"])){
        $patrimonio = $row["otherserial"];
    }
    else {
        $patrimonio = 'N/D';
    }

    if(isset($row["name"])){
        $nome = $row["name"];
    }
    else {
        $nome = 'N/D';
    }

    if(isset($row["status"])){
        $status = $row["status"];
    }
    else {
        $status = 'N/D';
    }

    if(isset($row["fabricante"])){
        $fabricante = $row["fabricante"];
    }
    else {
        $fabricante = 'N/D';
    }

    if(isset($row["nf_hw"])){
        $nf_hw = $row["nf_hw"];
    }
    else {
        $nf_hw = 'N/D';
    }

    if(isset($row["order_date"])){
        $dtcompra = $row["order_date"];
    }
    else {
        $dtcompra = 'N/D';
    }

    if(isset($row["value"])){
        $valor = $row["value"];
    }
    else {
        $valor = 'N/D';
    }

    if(isset($row["dep_mensal"])){
        $dep_mensal = $row["dep_mensal"];
    }
    else {
        $dep_mensal = 'N/D';
    }

    if(isset($row["old_cdc"])){
        $old_cdc = $row["old_cdc"];
    }
    else {
        $old_cdc = 'N/D';
    }

    if(isset($row["new_cdc"])){
        $new_cdc = $row["new_cdc"];
    }
    else {
        $new_cdc = 'N/D';
    }

    if(isset($row["data_modificacao"])){
        $data_modificacao = $row["data_modificacao"];
    }
    else {
        $data_modificacao = 'N/D';
    }

    echo     '<tr class="tab_bg_1">
            <td>'.$patrimonio.'</td>
            <td>'.$nome.'</td>
            <td>'.$status.'</td>
            <td>'.$fabricante.'</td>
            <td>'.$nf_hw.'</td>
            <td>'.$dtcompra.'</td>
            <td>'.$valor.'</td>
            <td>'.$dep_mensal.'</td>
            <td>'.$old_cdc.'</td>
            <td>'.$new_cdc.'</td>
            <td>'.$data_modificacao.'</td>';
    }
    echo         '</tr>
                    </tbody>
                </table>
                </form>
                </div>';
}
 Html::footer();

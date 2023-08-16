<?php

 include ("../../../inc/includes.php");

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

 $arquivo = 'RelatorioTrocaCDC.xls';
    $html = '<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />';
    $html .= '<table border="1">';
    $html .= '<tr style="background-color:#f0f0f0;">';
    $html .= '<td><b>Patrimônio</b></td>';
    $html .= '<td><b>Descrição</b></td>';
    $html .= '<td><b>Tipo</b></td>';
    $html .= '<td><b>Fabricante</b></td>';
    $html .= '<td><b>NF HW</b></td>';
    $html .= '<td><b>Data NF</b></td>';
    $html .= '<td><b>Valor Unitário</b></td>';
    $html .= '<td><b>Depreciação Mensal</b></td>';
    $html .= '<td><b>CDC Velho</b></td>';
    $html .= '<td><b>CDC Novo</b></td>';
    $html .= '<td><b>Data Alteração</b></td>';
    $html .= '</tr>';
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

    $html .=  '<tr class="tab_bg_1">';
    $html .=  '<td>'.$patrimonio.'</td>';
    $html .=  '<td>'.$nome.'</td>';
    $html .=  '<td>'.$status.'</td>';
    $html .=  '<td>'.$fabricante.'</td>';
    $html .=  '<td>'.$nf_hw.'</td>';
    $html .=  '<td>'.$dtcompra.'</td>';
    $html .=  '<td>'.$valor.'</td>';
    $html .=  '<td>'.$dep_mensal.'</td>';
    $html .=  '<td>'.$old_cdc.'</td>';
    $html .=  '<td>'.$new_cdc.'</td>';
    $html .=  '<td>'.$data_modificacao.'</td>';
    
    }

    // Configurações header para forçar o download
    header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
    header ("Cache-Control: no-cache, must-revalidate");
    header ("Pragma: no-cache");
    header ("Content-type: application/x-msexcel; charset=utf-8");
    header ("Content-Disposition: attachment; filename=\"{$arquivo}\"" );
    header ("Content-Description: PHP Generated Data" );
    // Envia o conteúdo do arquivo
    echo $html;
  exit;

?>
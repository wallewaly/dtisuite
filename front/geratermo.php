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

require '../../../inc/includes.php';

    global $DB;

    $dia = date("d") ; 
            $ano = date("Y") ;
            switch (date("m")) {
                case "01":    $mes = 'Janeiro';     break;
                case "02":    $mes = 'Fevereiro';   break;
                case "03":    $mes = 'Março';       break;
                case "04":    $mes = 'Abril';       break;
                case "05":    $mes = 'Maio';        break;
                case "06":    $mes = 'Junho';       break;
                case "07":    $mes = 'Julho';       break;
                case "08":    $mes = 'Agosto';      break;
                case "09":    $mes = 'Setembro';    break;
                case "10":    $mes = 'Outubro';     break;
                case "11":    $mes = 'Novembro';    break;
                case "12":    $mes = 'Dezembro';    break; 
            }

    //Obter dados do termo

    $query = "SELECT *
    FROM `glpi_plugin_dtisuite_loans`
    WHERE id = {$_GET['loanid']};";

    $result = $DB->queryOrDie($query, $DB->error());
    $first = $result->fetch_assoc();
    $equipid = $first['item_id'];
    $typeid = $first['itemtype_id'];
    $userid = $first['user_id'];
    $devoldate = $first['devoldate'];

    //Obter dados do equipamento

    $gtdata = new PluginDtisuiteData();
$equipmenttype = $gtdata->get_equipment_type($typeid);

    $query = "SELECT *
                FROM `glpi_".$equipmenttype."s`
                WHERE id = ".$equipid."
                ORDER BY name ASC;";

    $result = $DB->queryOrDie($query, $DB->error());
    $first = $result->fetch_assoc();
    $patrimonio = $first['otherserial'];
    $serial = $first['serial'];
    $nomeequip = $first['name'];
    $fabricanteid = $first['manufacturers_id'];
    $modelsid = $first[''.$equipmenttype.'models_id'];

    // Obter fabricante

    $query = "SELECT name
    FROM `glpi_manufacturers`
    WHERE id = ".$fabricanteid."
    ORDER BY name ASC;";

    $result = $DB->queryOrDie($query, $DB->error());
    $first = $result->fetch_assoc();
    $fabricante = $first['name'];

    //Obter modelo

    $query = "SELECT name
    FROM `glpi_".$equipmenttype."models`
    WHERE id = ".$modelsid."
    ORDER BY name ASC;";

    $result = $DB->queryOrDie($query, $DB->error());
    $first = $result->fetch_assoc();
    $modelo = $first['name'];

    //Obter dados do usuário

    $query = "SELECT *
    FROM `glpi_users`
    WHERE id = ".$userid."
    ORDER BY name ASC;";

    $result = $DB->queryOrDie($query, $DB->error());
    $first = $result->fetch_assoc();
    $nome = $first['firstname'];
    $sobrenome = $first['realname'];
    $nomecompleto = $nome.' '.$sobrenome;
    $cpf = $first['registration_number'];
    $base = $first['locations_id'];

    //obter dados do escritório

    if(!isset($base) || empty($base)){
        $base = 2;
    }

    $query = "SELECT *
    FROM `glpi_locations`
    WHERE id = ".$base."
    ORDER BY name ASC;";

    $result = $DB->queryOrDie($query, $DB->error());
    $first = $result->fetch_assoc();
    $cidade = $first['town'];
    $logradouro = $first['address'];
    $numeroend = $first['building'];
    $complemento = ' - '. $first['room'] .' - ';
    $estado = $first['state'];
    $cep = $first['postcode'];
    
    if(!isset($devoldate) || empty($devoldate)){
        

        $html = ' 
            <html style =" margin: 0 ; padding: 0;"  >
                    <head>
                        <meta charset="UTF-8"/>
                        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Open+Sans">

                            <title>Termo de Responsabilidade</title>
                    </head>

                    <body style = "text-align : justify;">
                        <img src="../img/LogoENG.svg" alt="" width=200 height=50 style =" margin: 30px 0 0 20px; padding: 0; ">
                        
                        <p style="text-align:center ; font-family: Arial, Helvetica, Sans-serif; font-size: 12pt; margin-top: 10px;"> <b>Termo de Responsabilidade - Equipamentos De TI</b>
                        <div style="width:100%;">
                            <div style="width:100%; height:3px; background:#c5004b;"></div>
                            <div style="width:100%; height:3px; background:#002f53;"></div>

                            
                            <br>

                        <p style =" margin: 0 40 0 40px; padding: 0 ; font-family: Arial, Helvetica, Sans-serif; font-size:10pt; ">
                        
                            Pelo presente termo, <b>DECLARO</b> ter recebido da Engineering do Brasil S/A, os equipamentos e acessórios,
                            conforme especificados abaixo, em perfeitas condições de uso, para a realização de minhas atividades enquanto 
                            empregado da empresa.

                        <p style = "font-family: Arial, Helvetica, Sans-serif; font-size: 10pt; margin: 15px 0 0 40px;"> <b>Equipamento :&nbsp;'.$patrimonio.'&nbsp;-&nbsp;'.$nomeequip.'</b>
                        <p style = "font-family: Arial, Helvetica, Sans-serif; font-size: 10pt; margin: 0 0 0 40px;"> <b>Marca:&nbsp;'.$fabricante.'</b>
                        <p style = "font-family: Arial, Helvetica, Sans-serif; font-size: 10pt; margin: 0 0 0 40px;"> <b>Modelo:&nbsp;'.$modelo.'</b>
                        <p style = "font-family: Arial, Helvetica, Sans-serif; font-size: 10pt; margin: 0 0 0 40px;"> <b>Nº De Série:&nbsp;'.$serial.' </b>
                        <p style = "font-family: Arial, Helvetica, Sans-serif; font-size: 10pt; margin: 0 0 0 40px;"> Acessórios:
                        <p style = "font-family: Arial, Helvetica, Sans-serif; font-size: 10pt; margin: 0 0 0 40px;"> CARREGADOR DE BATERIA
                        <p style = "font-family: Arial, Helvetica, Sans-serif; font-size: 10pt; margin: 0 0 0 40px;"> <b>Motivo Principal:</b> 
                        
                            <br>
                        <p style = "font-family: Arial, Helvetica, Sans-serif; font-size: 10pt; margin: 5px 0 0 40px;">  <input type="checkbox" style="position:relative; top:5px;">  Kit Admissional
                        <p style = "font-family: Arial, Helvetica, Sans-serif; font-size: 10pt; margin: 0 0 0 40px;">  <input type="checkbox" style="position:relative; top:5px;">  Outros (especificar):________________________________________________________________

                        <p style = "font-family: Arial, Helvetica, Sans-serif; font-size: 10pt; margin: 5px 0 0 40px;"> <b>Período de Uso:</b> 
                        <p style = "font-family: Arial, Helvetica, Sans-serif; font-size: 10pt; margin: 5px 0 0 40px;">  <input type="checkbox" style="position:relative; top:5px;">  Inderteminado
                        <p style = "font-family: Arial, Helvetica, Sans-serif; font-size: 10pt; margin: 5px 0 0 40px;">  <input type="checkbox" style="position:relative; top:5px;">  Determinado: _____ / _____ / _____ até _____ / _____ / _____. 
                        
                        
                        <p style =" margin: 10px 40px 0 40px; padding: 0 ; font-family: Arial, Helvetica, Sans-serif; font-size:10pt; "> <b> COMPROMETO-ME</b>, ainda, 
                                    a mantê-lo(s) em perfeito estado de conservação, ficando ciente de que:
                            <ul  "text-align : justify;">
                                <li style =" margin: 0 60px 2px 40px; padding: 0 ; font-family: Arial, Helvetica, Sans-serif; font-size:10pt; ">	
                                        Se o equipamento for danificado ou inutilizado por emprego inadequado,mau uso, negligência ou extravio, 
                                        para o qual tenha concorrido com culpa, comprometo-me a repor o equipamento nas mesmas condições de uso ou, alternativamente, 
                                        ressarcirei à ENGdB o valor do bem, atendendo à mesma especificação técnica ou equivalente ao bem recebido;
                            
                                <li style =" margin: 0 60px 2px 40px; padding: 0 ; font-family: Arial, Helvetica, Sans-serif; font-size:10pt; ">	
                                        Para os fins do item anterior, autorizo expressamente a dedução destes valores do salário.

                                <li style =" margin: 0 60px 2px 40px; padding: 0 ; font-family: Arial, Helvetica, Sans-serif; font-size:10pt; ">
                                        Em caso de furto ou roubo deverei apresentar à empresa o Boletim de Ocorrência Policial, informando a perda ou a ocorrência destes crimes. 
                                        Caso não apresente o competente Boletim de Ocorrência em 10 dias, estou ciente de que deverei ressarcir o bem ou repor o equipamento nos termos do item acima.
                            </ul>

                            <p style =" margin: 10px 60px 0 40px; padding: 0 ; font-family: Arial, Helvetica, Sans-serif; font-size:10pt; "> 
                                <b> DECLARO</b>, estar ciente da obrigatoriedade da necessidade de devolução do aparelho em perfeitas condições de funcionamento, bem como dos seus acessórios, 
                                considerando-se o desgaste natural pelo uso do bem, no momento em que a devolução for solicitada pela empresa ou no caso de rescisão contratual.
                            
                            <p style =" margin: 10px 60px 0 40px; padding: 0 ; font-family: Arial, Helvetica, Sans-serif; font-size:10pt; "> 

                                Considerando que os o(s) equipamento(s) recebido(s) foram concedidos pela empresa, em função do vinculo empregatício e, como uma ferramenta de trabalho, 
                                eles deverá(ão) ser utilizado(s) unica e exclusivamente por mim e para fins relativos aos serviços da empresa Engineering, não sendo permitida a utilização por qualquer 
                                outra pessoa, sob pena de ter que ressarcir eventuais prejuízos causados.

                            <p style =" margin: 10px 60px 0 40px; padding: 0 ; font-family: Arial, Helvetica, Sans-serif; font-size:10pt; "> 
                                SE O EQUIPAMENTO FOR NOTEBOOK / DESKTOP: O(s) equipamento(s) já possui(em) os softwares necessários para desenvolvimento das atividades profissionais e portanto não é permitido 
                                a instalação de quaisquer softwares, programas ou aplicativos adicionais não homologados e devidamente licenciados pela Engineering. 
                                A não observância destes termos poderá incorrer em advertência formal e passível de ação criminal e de ação cível de indenização conforme a legislação de softwares.
                            
                            <p style =" margin: 10px 60px 0 40px; padding: 0 ; font-family: Arial, Helvetica, Sans-serif; font-size:10pt; "> 

                            <br />

                            '.$cidade.', &nbsp;&nbsp;'.$dia.'&nbsp;de '.$mes.'&nbsp;de '.$ano.'

                            <p style = "text-align:center ; margin: 20px 0 0 0 ; padding: 0 ; font-family: Arial, Helvetica, Sans-serif; font-size:11pt ">
                                ______________________________________________________   <br />
                                                    '.$nomecompleto.'                <br />
                                                    '.$cpf. '                        <br />
                                        
                        
                        <div style="position:relative; background: #c5004b; height:20px; top:60px; color: #fff;">
                            <center><strong
                            <p style =" margin: 2 0 0 0; padding: 0 ; font-family: Arial, Helvetica, Sans-serif; font-size:8pt; "> 
                                Engineering Brasil - Escritório '.$cidade.'
                            </p>
                            </strong><center>
                        </div>
                        <div style="position:relative; background: #002f53; height:80px; top:60px; color: #fff;">
                            <center><strong>
                                <p style =" margin: 10px 60px 0 40px; padding: 0 ; font-family: Arial, Helvetica, Sans-serif; font-size:8pt; "> 
                                    '.$logradouro.', '.$numeroend.' '.$complemento.' '.$cidade.' - '.$estado.' - '.$cep.'
                                <br />
                                    Suporte Departamento de TI: <a style="color:#fff;" href="https://suporte-dti.engdb.com.br/">suporte-dti.engdb.com.br</a>
                                </p>
                            </strong><center>
                        </div>
                    </body>
                    </html>
            
            ';
        } else {
   
        $html = ' 
        <html style =" margin: 0 ; padding: 0;"  >
                <head>
                    <meta charset="UTF-8"/>
                    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Open+Sans">
                        <title>Termo de Responsabilidade</title>
                </head>

                <body style = "text-align : justify;">
                    <img src="../img/LogoENG.svg" alt="" width=200 height=50 style =" margin: 30px 0 0 20px; padding: 0; ">
                    
                    <p style="text-align:center ; font-family: Arial, Helvetica, Sans-serif; font-size: 12pt; margin-top: 10px;"> <b>Termo de Devolução - Equipamentos De TI</b>
                    <div style="width:100%;">
                        <div style="width:100%; height:3px; background:#c5004b;"></div>
                        <div style="width:100%; height:3px; background:#002f53;"></div>

                        
                        <br>

                        <p style =" margin: 0 40 0 60px; padding: 0 ; font-family: Arial, Helvetica, Sans-serif; font-size:10pt; ">
                        
                        Declaro estar devolvendo nesta data o(s) equipamento(s) discriminados na(s) condição(ões) abaixo apresentada(s):

                        <p style = "font-family: Arial, Helvetica, Sans-serif; font-size: 13pt; margin: 15px 0 0 60px;"> <b>Equipamento :&nbsp;'.$patrimonio.'&nbsp;-&nbsp;'.$nomeequip.'</b>
                        <p style = "font-family: Arial, Helvetica, Sans-serif; font-size: 13pt; margin: 0 0 0 60px;"> <b>Marca:&nbsp;'.$fabricante.'</b>
                        <p style = "font-family: Arial, Helvetica, Sans-serif; font-size: 13pt; margin: 0 0 0 60px;"> <b>Modelo:&nbsp;'.$modelo.'</b>
                        <p style = "font-family: Arial, Helvetica, Sans-serif; font-size: 13pt; margin: 0 0 0 60px;"> <b>Nº De Série:&nbsp;'.$serial.' </b>

                        <p style = "font-family: Arial, Helvetica, Sans-serif; font-size: 13pt; margin: 20px 0 0 60px;"> Acessórios:
                        <p style = "font-family: Arial, Helvetica, Sans-serif; font-size: 10pt; margin: 0 0 0 60px;"> CARREGADOR DE BATERIA
                        <p style = "font-family: Arial, Helvetica, Sans-serif; font-size: 13pt; margin: 30px 0 0 60px;"> <b>Condições do Equipamento:</b> 
                        <p style = "font-family: Arial, Helvetica, Sans-serif; font-size: 10pt; margin: 5px 0 0 60px;"> (&nbsp;&nbsp;&nbsp;&nbsp;) Em estado de conservação.
                        <p style = "font-family: Arial, Helvetica, Sans-serif; font-size: 10pt; margin: 5px 0 0 60px;"> (&nbsp;&nbsp;&nbsp;&nbsp;) Maquina Lenta
                        <p style = "text-align:center ; margin: 15px 0 0 0 ; padding: 0 ; font-family: Arial, Helvetica, Sans-serif; font-size:11pt ">_____________________________________________________________________________________________   
                        <p style = "font-family: Arial, Helvetica, Sans-serif; font-size: 10pt; margin: 15px 0 20px 60px;"> (&nbsp;&nbsp;&nbsp;&nbsp;) Faltando a(s) seguinte(s) peça(s) e/ou acessório(s): 
                        <p style = "text-align:center ; margin: 15px 0 0 0 ; padding: 0 ; font-family: Arial, Helvetica, Sans-serif; font-size:11pt ">_____________________________________________________________________________________________

                        
                        
                        <p style =" margin: 10px 60px 0 60px; padding: 0 ; font-family: Arial, Helvetica, Sans-serif; font-size:10pt; "> 

                        <br />

                        '.$cidade.', &nbsp;&nbsp;'.$dia.'&nbsp;de '.$mes.'&nbsp;de '.$ano.'

                        <p style = "text-align:center ; margin: 50px 0 0 0 ; padding: 0 ; font-family: Arial, Helvetica, Sans-serif; font-size:11pt ">
                            ______________________________________________________   <br />
                                                '.$nomecompleto.'                <br />
                                                '.$cpf. '                        <br />
                                                       
                    <div style="position:relative; background: #c5004b; height:40px; top:350px; color: #fff;">
                        <center><strong
                        <p style =" margin: 10px 60px 0 40px; padding: 0 ; font-family: Arial, Helvetica, Sans-serif; font-size:12pt; "> 
                            Engineering Brasil - Escritório '.$cidade.'
                        </p>
                        </strong><center>
                    </div>
                    <div style="position:relative; background: #002f53; height:80px; top:350px; color: #fff;">
                        <center><strong>
                            <p style =" margin: 10px 60px 0 40px; padding: 0 ; font-family: Arial, Helvetica, Sans-serif; font-size:10pt; "> 
                                '.$logradouro.', '.$numeroend.' '.$complemento.' '.$cidade.' - '.$estado.' - '.$cep.'
                            <br />
                                Suporte Departamento de TI: <a style="color:#fff;" href="https://suporte-dti.engdb.com.br">suporte-dti.engdb.com.br</a>
                            </p>
                        </strong><center>
                    </div>
                </body>
                </html>
        
        ';

        }
    
    $gtpdf = new PluginDtisuitePDF();
    $gtpdf->gen_pdf($html);
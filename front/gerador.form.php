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

    Html::header(__("Gerar termos", "helpdesk"), $_SERVER['PHP_SELF'],
                "management", "PluginDtisuiteMenutermo", "PluginDtisuiteContainer");

    Session::checkRight('plugin_dtisuite_gen_terms', READ);

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
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    <h4 class="card-title ps-4">
                                        Gerador de termo
                                    </h4>
                                </div>
                            

                            <div class="card-body d-flex flex-wrap">
                                <div class="col-12 col-xxl-12 flex-column">
                                    <div class="d-flex flex-row flex-wrap flex-xl-nowrap">
                                        <div class="row flex-row align-items-start flex-grow-1">
                                            <div class="row flex-row">

                                            <!-- Required fields  --!>

                                            <div class="form-field row col-12 col-sm-4  mb-2">
                                                    <label class="col-form-label col-xxl-5 text-xxl-end">
                                                        Tipo de termo
                                                    </label>
                                                    <div class="col-xxl-7  field-container">
                                                        <select name="tipoTermo" id="tipoTermo" class="form-select" data-select2-id="tipoTermo" onChange="dpd_control(this)">
                                                            <option value="" selected disabled>-----</option>
                                                            <option value="1">Empr&eacute;stimo</option>
                                                            <option value="2">Devolu&ccedil;&atilde;o</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-field row col-12 col-sm-4  mb-2" id="fieldtipoDevol" >
                                                    <label class="col-form-label col-xxl-5 text-xxl-end">
                                                        Tipo de devolução
                                                    </label>
                                                    <div class="col-xxl-7  field-container">
                                                        <select name="tipoDevol" id="tipoDevol" class="form-select" onChange="dpd_control(this)" disabled>
                                                            <option value="" selected disabled>-----</option>
                                                            <option value="1">Equipamento &uacute;nico</option>
                                                            <option value="2">Todos os equipamentos</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-field row col-12 col-sm-4  mb-2">
                                                    <label class="col-form-label col-xxl-5 text-xxl-end">
                                                        Tipo de item
                                                    </label>
                                                    <div class="col-xxl-7  field-container">
                                                        <select name="tipoItem" id="tipoItem" class="form-select" onChange="dpd_control(this)" disabled>
                                                            <option class="select2-results__option" value="" selected disabled>-----</option>
                                                            <option class="select2-results__option" value="1">Computador</option>
                                                            <option class="select2-results__option" value="2">Telefone/Modem</option>
                                                            <option class="select2-results__option" value="3">Monitor</option>
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <!-- End of required fields  --!>

                                    <!-- Optional or context fields  --!>

                                    <div class="d-flex flex-row flex-wrap flex-xl-nowrap">
                                        <div class="row flex-row align-items-start flex-grow-1">
                                            <div class="row flex-row">

                                                <div class="form-field row col-12 col-sm-6  mb-2">
                                                    <label class="col-form-label col-xxl-5 text-xxl-end">
                                                        Colaborador
                                                    </label>
                                                    <div class="col-xxl-7  field-container">
                                                        <select name="employee" id="employee" class="form-select" onChange="dpd_control(this)" disabled>
                                                            <option class="select2-results__option" value="" selected disabled>-----</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-field row col-12 col-sm-6  mb-2" id="fieldEquip" >
                                                    <label class="col-form-label col-xxl-5 text-xxl-end">
                                                        Equipamento
                                                    </label>
                                                    <div class="col-xxl-7  field-container">
                                                        <select name="equip" id="equip" class="form-select" onChange="dpd_control(this)" disabled>
                                                            <option class="select2-results__option" value="" selected disabled>-----</option>
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- End of optional or context fields  --!>

                                    <button class="btn btn-primary me-2 float-end mt-sm-3" name="gerar" id="gerar" onclick="term_gen()" disabled type="button">
                                        <span>Avançar</span>&nbsp;
                                        <i class="fas fa-angle-right"></i>
                                    </button>

                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </main>
        </div>
        <div class="card-body d-flex flex-wrap" id="results">
            <div class="col-12 col-xxl-12 flex-column">

                <div id="content" class="card-header d-flex justify-content-between search-header pe-0">
                    
                </div>
            </div>
        </div>
    </div>
    <script defer src="../js/geradortermos.js"></script>

    ';

Html::footer();
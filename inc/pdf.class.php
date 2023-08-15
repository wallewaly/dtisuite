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
 * @copyright Copyright (C) 2023 by DTISuite plugin team.
 * @license   GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/wallewaly/dtisuie
 * -------------------------------------------------------------------------
 */

include '../vendor/autoload.php';

use Dompdf\Dompdf;
    
class PluginDtisuitePDF extends CommonDBTM {

    function gen_pdf($html){

        // instantiate and use the dompdf class
        $dompdf = new Dompdf();

        // (Optional) Setup the paper size and orientation
        $chroot = realpath(__DIR__ . '/..');
        $dompdf->set_option('chroot',$chroot);
        $dompdf->set_option('isFontSubsettingEnabled',TRUE);
        
        $dompdf->setPaper('A4', 'portrait');
        

        // Render the HTML as PDF
        $dompdf->loadHtml($html);
        $dompdf->render();

        ob_end_clean();

        if (isset($_GET['download'])) {
            
            $dompdf->stream(
                            "GLPITermo.pdf", // Nome do arquivo de saída 
                            array(
                                "Attachment" => true // Para download, altere para true 
                            )
                        );
        } else {
           $dompdf->stream(
                            "GLPITermo.pdf", // Nome do arquivo de saída 
                            array(
                                "Attachment" => false // Para download, altere para true 
                            )
                    );
        }
    }
}
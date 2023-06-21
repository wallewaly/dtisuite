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

use Glpi\Plugin\Hooks;

// Plugin version
define("PLUGIN_DTISUITE_VERSION", "0.1.0");
// Minimal GLPI version, inclusive
define('PLUGIN_DTISUITE_GLPI_MIN_VERSION', '10.0.0');
// Maximum GLPI version, exclusive
define('PLUGIN_DTISUITE_GLPI_MAX_VERSION', '10.0.99');
// Plugin home dir
define('PLUGIN_DTISUITE_DIR', __DIR__);

/**
 * Init hooks of the plugin.
 * REQUIRED
 *
 * @return void
 */
function plugin_init_dtisuite() {

    global $PLUGIN_HOOKS, $CFG_GLPI;

    $PLUGIN_HOOKS['csrf_compliant']['dtisuite'] = true;

    $Plugin = new Plugin();

    if ($Plugin->isActivated('dtisuite')) {
        
        //Classes registration

        $Plugin->registerClass(
            'PluginDtisuiteProfile',
            ['addtabon' => ['Profile']
            ]
        );

        //Add menu entry
        $PLUGIN_HOOKS['menu_toadd']['dtisuite'] = ['management'  => 'PluginDtisuiteMenu'];

        //Add config menu entry
        $PLUGIN_HOOKS['config_page']['dtisuite'] = 'front/config.php';

        //Capture events data on action
        $PLUGIN_HOOKS['pre_item_update']['dtisuite'] = [
            'Computer' => 'dtisuite_itemimeiupdate_called',
            'Phone' => 'dtisuite_itemimeiupdate_called'
        ];

        //Add form on top of item page
        $PLUGIN_HOOKS['pre_item_form']['dtisuite']
        = 'plugin_dtisuite_computerpreItemForm';

        // Css file
        if (strpos($_SERVER['REQUEST_URI'] ?? '', Plugin::getPhpDir('dtisuite', false)) !== false) {
             $PLUGIN_HOOKS['add_css']['dtisuite'] = 'css/dtisuite.css';
        }
     
    }

}

/**
 * Get the name and the version of the plugin
 * REQUIRED
 *
 * @return array
 */
function plugin_version_dtisuite() {
    return [
       'name'           => 'DTI Suite',
       'version'        => PLUGIN_DTISUITE_VERSION,
       'author'         => 'Walison Santos',
       'license'        => 'GPLv2+',
       'homepage'       => '',
       'requirements'   => [
          'glpi' => [
             'min' => '10.0',
          ]
       ]
    ];
 }


/**
 * Blocking a specific version of GLPI.
 * GLPI constantly evolving in terms of functions of the heart, it is advisable
 * to create a plugin blocking the current version, quite to modify the function
 * to a later version of GLPI. In this example, the plugin will be operational
 * with the 0.84 and 0.85 versions of GLPI.
 *
 * @return boolean
 */
function plugin_dtisuite_check_prerequisites()
{

    if (version_compare(GLPI_VERSION, PLUGIN_DTISUITE_GLPI_MIN_VERSION, 'lt') || version_compare(GLPI_VERSION, PLUGIN_DTISUITE_GLPI_MAX_VERSION, 'gt')) {
        if (method_exists('Plugin', 'messageIncompatible')) {
			//since GLPI 9.2
			Plugin::messageIncompatible('core', PLUGIN_DTISUITE_GLPI_MIN_VERSION, PLUGIN_DTISUITE_GLPI_MAX_VERSION);
		} else {
			echo "Este plugin requer o GLPI >= ".PLUGIN_DTISUITE_GLPI_MIN_VERSION." e GLPI <= ".PLUGIN_DTISUITE_GLPI_MAX_VERSION;
        }
		return false;
    }

    return true;
}

/**
 * Control of the configuration
 *
 * @param type $verbose
 * @return boolean
 */
function plugin_dtisuite_check_config($verbose = false)
{
    if (true) { // Your configuration check
       return true;
    }

    if ($verbose) {
        echo 'Installed / not configured';
    }

    return false;
}
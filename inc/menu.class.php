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

class PluginDtisuiteMenu extends CommonGLPI
{

#   static $rightname = 'entities';

   static function getMenuName() {

      return __('Gerador de termos (v2)', 'dtisuite');
   }

   static function getMenuContent() {

      global $CFG_GLPI;

      $dtisuiteurl = "/".Plugin::getWebDir('dtisuite', false).'/front/gerador.form.php';

      $menu = [
         'title' => self::getMenuName(),
         'page'  => $dtisuiteurl,
         'icon'  => 'fas fa-file-pdf',
      ];

      return $menu;
      
   }

}
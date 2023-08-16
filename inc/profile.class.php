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

class PluginDtisuiteProfile extends Profile
{

   static $rightname = "profile";

   static function getAllRights() {

      $rights = [
        [     'label'     => __('Gerador de termos', 'dtisuite'),
              'field'     => 'plugin_dtisuite_gen_terms',
              'rights'    => [READ => __('Read')]],
            
        [     'label'     => __('Relatório de troca de CDC', 'dtisuite'),
        'field'     => 'plugin_dtisuite_cdc_report',
        'rights'    => [READ => __('Read')]]];

      return $rights;
   }

    /**
    * Clean profiles_id from plugin's profile table
    *
    * @param $ID
   **/
   function cleanProfiles($ID) {

      global $DB;
      $query = "DELETE FROM `glpi_profiles`
                WHERE `profiles_id`='$ID'
                   AND `name` LIKE '%plugin_DTISuite%'";
      $DB->queryOrDie($query, $DB->error());
   }

   function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {

      if ($item->getType() == 'Profile') {
         if ($item->getField('interface') == 'central') {
            return __('DTISuite', 'DTISuite');
         }
         return '';
      }
      return '';
   }


   static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {

      if ($item->getType() == 'Profile') {
         $profile = new self();
         $ID   = $item->getField('id');
         //In case there's no right DTISuite for this profile, create it
         self::addDefaultProfileInfos(
             $item->getID(),
             ['plugin_dtisuite_gen_terms' => 0]
         );
         $profile->showForm($ID);
      }
      return true;
   }

    /**
    * @param $profile
   **/
   static function addDefaultProfileInfos($profiles_id, $rights) {

      $profileRight = new ProfileRight();
      foreach ($rights as $right => $value) {
         if (!countElementsInTable(
             'glpi_profilerights',
             ['profiles_id' => $profiles_id, 'name' => $right]
         )) {
            $myright['profiles_id'] = $profiles_id;
            $myright['name']        = $right;
            $myright['rights']      = $value;
            $profileRight->add($myright);

            //Add right to the current session
            $_SESSION['glpiactiveprofile'][$right] = $value;
         }
      }
   }

    /**
    * @param $ID  integer
    */
   static function createFirstAccess($profiles_id) {

      include_once Plugin::getPhpDir('DTISuite')."/inc/profile.class.php";
      foreach (self::getAllRights() as $right) {
         self::addDefaultProfileInfos(
             $profiles_id,
             ['plugin_dtisuite_gen_terms' => READ]
         );
      }
   }

   function showForm($ID, $options = []) {

      echo "<div class='firstbloc'>";
      if ($canedit = Session::haveRightsOr(self::$rightname, [CREATE, UPDATE, PURGE])) {
         $profile = new Profile();
         echo "<form method='post' action='".$profile->getFormURL()."'>";
      }

      $profile = new Profile();
      $profile->getFromDB($ID);

      $rights = self::getAllRights();
      $profile->displayRightsChoiceMatrix(
          $rights,
          [
             'canedit'       => $canedit,
             'default_class' => 'tab_bg_2',
             'title'         => __('General')
          ]
      );
      if ($canedit) {
         echo "<div class='center'>";
         echo Html::hidden('id', ['value' => $ID]);
         echo Html::submit(_sx('button', 'Save'), ['name' => 'update']);
         echo "</div>\n";
         Html::closeForm();
      }
       echo "</div>";
   }
}

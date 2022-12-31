<?php
/**
 * @package   System - ZOO Event
 * @author    YOOtheme https://yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Session\Session;
use Joomla\Registry\Registry;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;

// use Joomla\CMS\Filesystem\Folder;
// use Joomla\CMS\Filesystem\File;
// use Joomla\CMS\HTML\HTMLHelper;
// use Joomla\CMS\Log\Log;

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgSystemCleanunusedzooapps extends CMSPlugin {

    /**
     * onAfterInitialise handler
     *
     * Adds ZOO event listeners
     *
     * @access public
     * @return null
     */
    public function onAfterInitialise()
    {
        // make sure ZOO exists
        if (!ComponentHelper::getComponent('com_zoo', true)->enabled) {
            return;
        }

        // load ZOO config
       if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php')) {
           return;
       }
       require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');
       if (!ComponentHelper::getComponent('com_zoo', true)->enabled) {
           return;
       }

        // make sure App class exists
        if (!class_exists('App')) {
            return;
        }
        $input = Factory::getApplication()->input;      

        if ($input->get('option', false) != 'com_zoo' || $input->get('controller', false) != 'manager')
        {
            return;
        }

        $apps_in_use = json_encode(self::getAppsInUse());

        // Load Global JS Scripts
		if (!Factory::getApplication()->isClient('site'))
        {

            Factory::getDocument()->addScriptDeclaration("
                jQuery(document).ready(function($) {
                    var apps_in_use = JSON.parse('$apps_in_use');
                    console.log(apps_in_use);
                    $('.application-list a').each(function(index, value) {
                        if (!apps_in_use.includes($(this).attr('href').split('&').slice(-1)[0].split('=')[1]))
                        {
                            $(this).hide();
                        }
                    }); 
                });
            ");
        }

    }

    // public function onAjaxCleanunusedzooapps()
    // {
    //     $app = Factory::getApplication();
        
    //     if ($app->input->get('token') != Session::getFormToken()) return [JText::_('JINVALID_TOKEN')];

    //     $method = $app->input->getString('method');
    //     $data   = $app->input->getJson('contentData');

    //     return self::$method($data);
    // }
    
    public function getAppsInUse() {

        $zoo  = App::getInstance('zoo');

        $all_apps = $zoo->table->application->find();

        $app_groups = [];
        foreach ($all_apps as $app) {
            $app_groups[] = $app->application_group;
        }
        
        return $app_groups;
    }

}

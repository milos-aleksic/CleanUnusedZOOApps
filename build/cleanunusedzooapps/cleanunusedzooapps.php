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

        // Get the ZOO App instance
        $zoo = App::getInstance('zoo');

        // Load Global JS Scripts
		if (!Factory::getApplication()->isClient('site'))
        {
            $base_url = JURI::base(true);
            // HTMLHelper::_('jquery.framework');
            Factory::getDocument()->addScriptDeclaration("

                async function GlobalCleanUnusedZOOapps(method, contentData)
                {
        
                    if (!contentData) contentData = {};
        
                    return await $.ajax({
                        type: 'POST',
                        url: '" . Route::_('index.php?option=com_ajax&group=system&plugin=cleanunusedzooapps&format=json', false) . "',
                        data: {
                            token: '" . Session::getFormToken() . "',
                            method: method,
                            contentData: contentData
                        },
                        success: function(data)
                        {
                            return data;
                        }
                    });
        
                };
            ");
			$wa  = Factory::getDocument()->getWebAssetManager();

            if (!$wa->assetExists('script', 'cleanunusedzooapps'))
            {
                $wa->registerScript('cleanunusedzooapps_js', 'plugins/system/cleanunusedzooapps/assets/cleanunusedzooapps.js');
                $wa->useScript('jquery');
                $wa->useScript('cleanunusedzooapps_js');
            }
        }

    }

    public function onAjaxCleanunusedzooapps()
    {
        $app = Factory::getApplication();
        
        if ($app->input->get('token') != Session::getFormToken()) return [JText::_('JINVALID_TOKEN')];

        $method = $app->input->getString('method');
        $data   = $app->input->getJson('contentData');

        return self::$method($data);
    }
    
    public function cleanApps() {

        $zoo  = App::getInstance('zoo');

        $all_apps = $zoo->table->application->find();
        
        return $all_apps;
    }

}

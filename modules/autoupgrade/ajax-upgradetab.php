<?php
/*
* 2007-2011 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 8486 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

info("todo : improvements");
if($_POST['action'] == 'upgradeDb')
	require_once(dirname(__FILE__).'/../../config/settings.inc.php');

// ajax-upgrade-tab is located in admin/autoupgrade directory
require_once(realpath(dirname(__FILE__).'/../../').'/config/settings.inc.php');
if (!defined('_PS_MODULE_DIR_'))
	define('_PS_MODULE_DIR_', realpath(dirname(__FILE__).'/../../') .'/modules/');

define('AUTOUPGRADE_MODULE_DIR', _PS_MODULE_DIR_.'autoupgrade/');
require_once(AUTOUPGRADE_MODULE_DIR.'functions.php');
//
// dir = admin-dev
if ( realpath(dirname(__FILE__).'/../../').DIRECTORY_SEPARATOR.$_POST['dir'] !==  realpath(realpath(dirname(__FILE__).'/../../').DIRECTORY_SEPARATOR.$_POST['dir']))
	die('not allowed');

define('_PS_ADMIN_DIR_', realpath(dirname(__FILE__).'/../../').DIRECTORY_SEPARATOR.$_POST['dir']);
define('PS_ADMIN_DIR', _PS_ADMIN_DIR_); // Retro-compatibility
//require(_PS_ADMIN_DIR_.'/functions.php');
include(AUTOUPGRADE_MODULE_DIR.'init.php');

$adminObj = new $tab;
$adminObj->ajax = true;
	{

		if (is_object($adminObj))
		{
			if ($adminObj->checkToken())
			{
				// the differences with index.php is here 

				$adminObj->ajaxPreProcess();
				$action = Tools14::getValue('action');

				// no need to use displayConf() here

				if (!empty($action) AND method_exists($adminObj, 'ajaxProcess'.Tools14::toCamelCase($action)) )
					$adminObj->{'ajaxProcess'.Tools14::toCamelCase($action)}();
				else
					$adminObj->ajaxProcess();

				// @TODO We should use a displayAjaxError
				$adminObj->displayErrors();
				if (!empty($action) AND method_exists($adminObj, 'displayAjax'.Tools14::toCamelCase($action)) )
					$adminObj->{'displayAjax'.$action}();
				else
					$adminObj->displayAjax();

			}
			else
			{
				// If this is an XSS attempt, then we should only display a simple, secure page
				ob_clean();
				$adminObj->displayInvalidToken();

			}
		}
	}




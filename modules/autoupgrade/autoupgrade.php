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
*  @version  Release: $Revision: 10460 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class Autoupgrade extends Module
{
	function __construct()
	{
		$this->name = 'autoupgrade';
		$this->tab = 'administration';
		// version number x.y.z 
		// y+1 means a major bugfix or improvement
		// z+1 means a bugfix
		$this->version = '0.2.2';

		if (!defined('_PS_ADMIN_DIR_'))
		{
			if (defined('PS_ADMIN_DIR'))
				define('_PS_ADMIN_DIR_',PS_ADMIN_DIR);
			else
			{
				$this->_errors[] = $this->l('This version of PrestaShop cannot be upgraded : PS_ADMIN_DIR constant is missing');
				$autoupgradeCanWork = false;
			}
		}

		parent::__construct();

		$this->displayName = $this->l('Autoupgrade module');
		$this->description = $this->l('Provides an automated method to upgrade your shop to the last PrestaShop version. Caution : custom theme are not updated.');

	}
	function install()
	{
		$res = true;
		// before adding AdminSelfUpgrade, we should remove AdminUpgrade
		$idTab = Tab::getIdFromClassName('AdminUpgrade');

		if ($idTab)
		{
			$tab = new Tab($idTab);
			$res &= $tab->delete();
		}
		
		$idTab = Tab::getIdFromClassName('AdminSelfUpgrade');
		// Then we add AdminSelfUpgrade only if not exists
		if (!$idTab)
		{
			$tab = new Tab();
			$tab->class_name = 'AdminSelfUpgrade';
			$tab->module = 'autoupgrade';
			$tab->id_parent = 9;
			$languages = Language::getLanguages(false);
			foreach ($languages as $lang)
				$tab->name[$lang['id_lang']] = 'Upgrade';
			$res &= $tab->save();
		}
		else
			$tab = new Tab($idTab);
		Configuration::updateValue('PS_AUTOUPDATE_MODULE_IDTAB',$tab->id);

		$autoupgradeDir = _PS_ADMIN_DIR_.DIRECTORY_SEPARATOR.'autoupgrade';
		if (!file_exists($autoupgradeDir))
			$res &= @mkdir($autoupgradeDir);
		if (file_exists($autoupgradeDir.DIRECTORY_SEPARATOR.'ajax-upgradetab.php'))
			$res &= unlink($autoupgradeDir.DIRECTORY_SEPARATOR.'ajax-upgradetab.php');
		if (!defined('_PS_MODULE_DIR_'))
		{
			define('_PS_MODULE_DIR_', _PS_ROOT_DIR_.'/modules/');
		}
		
		$res &= copy(_PS_MODULE_DIR_.'autoupgrade/ajax-upgradetab.php',$autoupgradeDir . DIRECTORY_SEPARATOR . 'ajax-upgradetab.php');
		$res &= copy(_PS_MODULE_DIR_.'autoupgrade/logo.gif',_PS_ROOT_DIR_. DIRECTORY_SEPARATOR . 'img/t/AdminSelfUpgrade.gif');

		if (!$res 
			OR !Tab::getIdFromClassName('AdminSelfUpgrade')
			OR !parent::install()
		)
			return false;

		return true;
	}
	public function uninstall()
	{
		$id_tab = Configuration::get('PS_AUTOUPDATE_MODULE_IDTAB');
		if ($id_tab)
		{
			$tab = new Tab($id_tab,1);
			$res = $tab->delete();
		}
		else
			$res = true;
		// for people in 1.4.4.0 or 1.4.4.1, we have to remove that file
		// and of course delete it in the database.
		if (file_exists(_PS_ADMIN_DIR_.DIRECTORY_SEPARATOR.'tabs'.'AdminUpgrade.php'))
		{
			if($idOldTab = Tab::getIdFromClassName('AdminUpgrade'))
			{
				$tab = new Tab($idOldTab);
				$res &= $tab->delete();
			}
			$res &= unlink(_PS_ADMIN_DIR_.DIRECTORY_SEPARATOR.'tabs'.'AdminUpgrade.php');
		}
		
		if (file_exists(_PS_ADMIN_DIR_.DIRECTORY_SEPARATOR.'autoupgrade'.DIRECTORY_SEPARATOR.'ajax-upgradetab.php'))
			$res &= @unlink(_PS_ADMIN_DIR_.DIRECTORY_SEPARATOR.'autoupgrade'.DIRECTORY_SEPARATOR.'ajax-upgradetab.php');
		if (!$res OR !parent::uninstall())
			return false;

		return true;
	}


}

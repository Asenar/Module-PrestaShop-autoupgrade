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
*	@author PrestaShop SA <contact@prestashop.com>
*	@copyright	2007-2011 PrestaShop SA
*	@version	Release: $Revision: 10463 $
*	@license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*/


if(!defined('_PS_ADMIN_DIR_'))
	define('_PS_ADMIN_DIR_', PS_ADMIN_DIR);
if(!defined('_PS_USE_SQL_SLAVE_'))
	define('_PS_USE_SQL_SLAVE_',false);

if(empty($_POST['action']) OR !in_array($_POST['action'],array('upgradeDb')))
{
	if(!defined('_PS_CACHE_ENABLED_'))
		define('_PS_CACHE_ENABLED_',false);

	if(!defined('PS_ORDER_PROCESS_STANDARD'))
		define('PS_ORDER_PROCESS_STANDARD',true);
	if(!defined('PS_ORDER_PROCESS_OPC'))
		define('PS_ORDER_PROCESS_OPC',true);
	require_once(dirname(__FILE__).'/ConfigurationTest.php');
	eval('class ConfigurationTest extends ConfigurationTestCore{}');
}
	require_once(dirname(__FILE__).'/AdminSelfTab.php');
	require_once(dirname(__FILE__).'/SelfModule.php');

	// Add Upgrader class : if > 1.4.5.0 , uses core class
	// otherwise, use Upgrader.php in modules.
	// in both cases, use override if files exists
	if (!version_compare(_PS_VERSION_,'1.4.6.1','<') && file_exists(_PS_ROOT_DIR_.'/classes/Upgrader.php'))
		require_once(_PS_ROOT_DIR_.'/classes/Upgrader.php');
	else
		require_once(dirname(__FILE__).'/Upgrader.php');

	if (!class_exists('Upgrader',false))
	{
		if(file_exists(_PS_ROOT_DIR_.'/override/classes/Upgrader.php'))
			require_once(_PS_ROOT_DIR_.'/override/classes/Upgrader.php');
		else
			eval('class Upgrader extends UpgraderCore{}');
	}


require_once(dirname(__FILE__).'/Tools14.php');
if(!class_exists('Tools',false))
	eval('class Tools extends Tools14{}');
class AdminSelfUpgrade extends AdminSelfTab
{
	public $svn_link = 'http://svn.prestashop.com/trunk';

	public $ajax = false;
	public $nextResponseType = 'json'; // json, xml
	public $next = 'N/A';

	public $upgrader = null;
	public $standalone = true;

	/**
	 * set to false if the current step is a loop
	 *
	 * @var boolean
	 */
	public $stepDone = true;
	public $status = true;
	public $error ='0';
	public $nextDesc = '.';
	public $nextParams = array();
	public $nextQuickInfo = array();
	public $currentParams = array();
	/**
	 * @var array theses values will be automatically added in "nextParams"
	 * if their properties exists
	 */
	public $ajaxParams = array(
		// autoupgrade options
		'dontBackupImages',
		'keepDefaultTheme',
		'keepTrad',
		'manualMode',
		'desactivateCustomModule',

		'backupDbFilename',
		'backupFilesFilename',


	);
	public $autoupgradePath = null;
	/**
	 * autoupgradeDir
	 *
	 * @var string directory relative to admin dir
	 */
	public $autoupgradeDir = 'autoupgrade';
	public $latestRootDir = '';
	public $prodRootDir = '';
	public $adminDir = '';
	public $rootWritable = false;

	public $lastAutoupgradeVersion = '';
	public $svnDir = 'svn';
	public $destDownloadFilename = 'prestashop.zip';
	public $toUpgradeFileList = 'filesToUpgrade.list';
	public $toBackupFileList = 'filesToBackup.list';

	public $toRemoveFileList = 'filesToRemove.list';
	public $fromArchiveFileList = 'filesFromArchive.list';
	public $sampleFileList = array();
	private $backupIgnoreFiles = array();
	private $backupIgnoreAbsoluteFiles = array();
	private $excludeFilesFromUpgrade = array();
	private $excludeAbsoluteFilesFromUpgrade = array();

	private $backupFilesFilename = '';
	private $backupDbFilename = '';

/**
 * int loopBackupFiles : if your server has a low memory size, lower this value
 * @TODO remove the static, add a const, and use it like this : min(AdminUpgrade::DEFAULT_LOOP_ADD_FILE_TO_ZIP,Configuration::get('LOOP_ADD_FILE_TO_ZIP');
 */
	public static $loopBackupFiles = 1000;
/**
 * int loopUpgradeFiles : if your server has a low memory size, lower this value
 */
	public static $loopUpgradeFiles = 1000;
/**
 * int loopRemoveSamples : if your server has a low memory size, lower this value
 */
	public static $loopRemoveSamples = 1000;
/**
 * int loopRemoveUpgraderFiles : remove files to keep only files that where in backup archive
 */
	public static $loopRemoveUpgradedFiles = 1000;

	/* usage :  key = the step you want to ski
  * value = the next step you want instead
 	*	example : public static $skipAction = array('download' => 'upgradeFiles');
	*/
	public static $skipAction = array();

	public $useSvn;
	public static $force_pclZip = false;

	protected $_includeContainer = false;

	public function encrypt($string)
	{
		return md5(_COOKIE_KEY_.$string);
	}
	public function checkToken()
	{
		// simple checkToken in ajax-mode, to be free of Cookie class (and no Tools::encrypt() too )
		if ($this->ajax)
			return ($_COOKIE['autoupgrade'] == $this->encrypt($_COOKIE['id_employee']));
		else
			return parent::checkToken();
	}

	/**
	 * create cookies id_employee, id_tab and autoupgrade (token)
	 */
	public function createCustomToken()
	{
		// ajax-mode for autoupgrade, we can't use the classic authentication
		// so, we'll create a cookie in admin dir, based on cookie key 
		global $cookie;
		$id_employee = $cookie->id_employee;

		$cookiePath = __PS_BASE_URI__.str_replace($this->prodRootDir,'',trim($this->adminDir,DIRECTORY_SEPARATOR));
		setcookie('id_employee', $id_employee, time()+3600, $cookiePath);
		setcookie('id_tab', $this->id, time()+3600, $cookiePath);
		setcookie('autoupgrade', $this->encrypt($id_employee), time()+3600, $cookiePath);
		return false;
	}



	public function viewAccess($disable = false){
		if ($this->ajax)
			return true;
		else
		{
			// simple access : we'll allow only admin
			global $cookie;
			if ($cookie->profile == 1)
				return true;
		}
		return false;
	}

	public function __construct()
	{
		@set_time_limit(0);
		@ini_set('max_execution_time', '0');

		$this->init();
		// retrocompatibility when used in module : Tab can't work,
		// but we saved the tab id in a cookie.
		if(class_exists('Tab',false))
			parent::__construct();
		else
			$this->id = $_COOKIE['id_tab'];
	}

	protected function l($string, $class = 'AdminTab', $addslashes = FALSE, $htmlentities = TRUE)
	{
		if(version_compare(_PS_VERSION_,'1.4.3.0','<'))
		{
			$currentClass = get_class($this);
			// need to be called in order to populate $classInModule
			return SelfModule::findTranslation('autoupgrade', $string, 'AdminSelfUpgrade');
		}
		else
			return parent::l($string, $class, $addslashes, $htmlentities);
	}

	/**
	 * _setFields function to set fields (only when we need it).
	 *
	 * @return void
	 */
	private function _setFields()
	{
		$this->_fieldsAutoUpgrade['PS_AUTOUP_DONT_SAVE_IMAGES'] = array(
			'title' => $this->l('Also save images'), 'cast' => 'intval', 'validation' => 'isBool',
			'type' => 'bool', 'desc'=>$this->l('You can exclude the image directory from backup if you already saved it by another method (not recommended)'),
		);

		$this->_fieldsAutoUpgrade['PS_AUTOUP_KEEP_DEFAULT_THEME'] = array(
			'title' => $this->l('Keep theme "prestashop"'), 'cast' => 'intval', 'validation' => 'isBool',
			'type' => 'bool', 'desc'=>$this->l('If you have customized PrestaShop default theme, you can protect it from upgrade (not recommended)'),
		);

		$this->_fieldsAutoUpgrade['PS_AUTOUP_KEEP_TRAD'] = array(
			'title' => $this->l('Keep translations'), 'cast' => 'intval', 'validation' => 'isBool',
			'type' => 'bool', 'desc'=>$this->l('If set to yes, you will keep all your translations'),
		);

		$this->_fieldsAutoUpgrade['PS_AUTOUP_CUSTOM_MOD_DESACT'] = array(
			'title' => $this->l('Deactivate custom modules'), 'cast' => 'intval', 'validation' => 'isBool',
			'type' => 'bool', 'desc'=>$this->l('If you don\'t deactivate your modules, you can have some compatibility problems and the Modules page might not load correctly.'),
		);
		// allow manual mode only for dev
		if (defined('_PS_MODE_DEV_') AND _PS_MODE_DEV_)
			$this->_fieldsAutoUpgrade['PS_AUTOUP_MANUAL_MODE'] = array(
				'title' => $this->l('Manual mode'),	'cast' => 'intval',	'validation' => 'isBool',
				'type' => 'bool',	'desc'=>$this->l('Check this if you want to stop after each step'),
			);

		if (defined('_PS_ALLOW_UPGRADE_UNSTABLE_') AND _PS_ALLOW_UPGRADE_UNSTABLE_ AND function_exists('svn_checkout'))
		{
			$this->_fieldsAutoUpgrade['PS_AUTOUP_USE_SVN'] = array(
				'title' => $this->l('Use Subversion'), 'cast' => 'intval', 'validation' => 'isBool',
				'type' => 'bool',	'desc' => $this->l('check this if you want to use unstable svn instead of official release'),
			);
		}
	}

	public function configOk()
	{
		$allowed_array = $this->getCheckCurrentConfig();
		$allowed = array_product($allowed_array);
		return $allowed;
	}

	public function getcheckCurrentConfig()
	{
		static $allowed_array;

		if(empty($allowed_array))
		{
			$allowed_array = array();
			$allowed_array['fopen'] = ConfigurationTest::test_fopen();
			$allowed_array['root_writable'] = $this->rootWritable;
			$allowed_array['shop_enabled'] = !Configuration::get('PS_SHOP_ENABLE');
			$allowed_array['autoupgrade_allowed'] = $this->upgrader->autoupgrade;
			if ($module_version = simplexml_load_file(dirname(__FILE__).'/config.xml'))
			{
				$module_version = (string)$module_version->version;
				$allowed_array['module_version_ok'] = version_compare($module_version, $this->upgrader->autoupgrade_last_version, '>=');
			}
			else
			{
				$allowed_array['module_version_ok'] = 1; // unable to check, let's assume it's ok 

			}
				
			// if one option has been defined, all options are.
			$allowed_array['module_configured'] = (Configuration::get('PS_AUTOUP_KEEP_TRAD') !== false);
		}
		return $allowed_array;
	}


	public function checkAutoupgradeLastVersion(){
		if ($xml_module_version = simplexml_load_file(_PS_MODULE_DIR_.'autoupgrade'.'/config.xml'))
			$module_version = (string)$xml_module_version->version;
		else
			$module_version = 'error';
		
		$this->lastAutoupgradeVersion = version_compare($this->upgrader->autoupgrade_last_version, $module_version, '<=');
		return $this->lastAutoupgradeVersion;
	}

	/**
	 * isUpgradeAllowed checks if all server configuration is valid for upgrade
	 *
	 * @return void
	 */
	public function isUpgradeAllowed()
	{
		$allowed = (ConfigurationTest::test_fopen() && $this->rootWritable);

		if (!defined('_PS_MODE_DEV_') OR !_PS_MODE_DEV_)
			$allowed &= $this->upgrader->autoupgrade_module;

		return $allowed;
	}

	/**
	 * init to build informations we need
	 *
	 * @return void
	 */
	public function init()
	{
		// For later use, let's set up prodRootDir and adminDir
		// This way it will be easier to upgrade a different path if needed
		$this->prodRootDir = _PS_ROOT_DIR_;
		$this->adminDir = _PS_ADMIN_DIR_;

		// from $_POST or $_GET
		$this->action = empty($_REQUEST['action'])?null:$_REQUEST['action'];
		$this->currentParams = empty($_REQUEST['params'])?null:$_REQUEST['params'];
		// test writable recursively
		if(version_compare(_PS_VERSION_,'1.4.6.0','<') || !class_exists('ConfigurationTest', false))
		{
			require_once('ConfigurationTest.php');
			if(!class_exists('ConfigurationTest', false) AND class_exists('ConfigurationTestCore'))
				eval('class ConfigurationTest extends ConfigurationTestCore{}');
		}
		if (ConfigurationTest::test_dir($this->prodRootDir,true))
			$this->rootWritable = true;

		if (!in_array($this->action, array('upgradeFile', 'upgradeDb', 'upgradeComplete','rollback','restoreFiles','restoreDb', 'checkFilesVersion')))
		{
		// checkPSVersion will be not

			$this->upgrader = new Upgrader();
			$this->upgrader->checkPSVersion();
			$this->nextParams['install_version'] = $this->upgrader->version_num;
		}
		// If you have defined this somewhere, you know what you do
		if (defined('_PS_ALLOW_UPGRADE_UNSTABLE_') AND _PS_ALLOW_UPGRADE_UNSTABLE_ AND function_exists('svn_checkout'))
		{
			if(version_compare(_PS_VERSION_,'1.4.5.0','<') OR class_exists('Configuration',false))
				$this->useSvn = Configuration::get('PS_AUTOUP_USE_SVN');
		}
		else
			$this->useSvn = false;


		// If not exists in this sessions, "create"
		// session handling : from current to next params
		if (isset($this->currentParams['removeList']))
			$this->nextParams['removeList'] = $this->currentParams['removeList'];

		if (isset($this->currentParams['filesToUpgrade']))
			$this->nextParams['filesToUpgrade'] = $this->currentParams['filesToUpgrade'];

		// set autoupgradePath, to be used in backupFiles and backupDb config values
		$this->autoupgradePath = $this->adminDir.DIRECTORY_SEPARATOR.$this->autoupgradeDir;

		if (!file_exists($this->autoupgradePath))
			if (!@mkdir($this->autoupgradePath,0777))
				$this->_errors[] = Tools::displayError(sprintf($this->l('unable to create directory %s'),$this->autoupgradePath));

		$latest = $this->autoupgradePath.DIRECTORY_SEPARATOR.'latest';
		if (!file_exists($latest))
			if (!@mkdir($latest,0777))
				$this->_errors[] = Tools::displayError(sprintf($this->l('unable to create directory %s'),$latest));

		if (class_exists('Configuration',false))
		{
			$time = time();
			$this->backupDbFilename = Configuration::get('UPGRADER_BACKUPDB_FILENAME');
			if(!file_exists($this->backupDbFilename))
			{
				// If not exists, the filename is generated by Backup.php
				$this->backupDbFilename = '';
				Configuration::updateValue('UPGRADER_BACKUPDB_FILENAME', $this->backupDbFilename);
			}

			$this->backupFilesFilename = Configuration::get('UPGRADER_BACKUPFILES_FILENAME');
			if(!file_exists($this->backupFilesFilename))
			{
				$this->backupFilesFilename = $this->autoupgradePath . DIRECTORY_SEPARATOR . 'backupfile-'.date('Y-m-d').'-'.$time.'.zip';
				Configuration::updateValue('UPGRADER_BACKUPFILES_FILENAME', $this->backupFilesFilename);
			}
		}
		else{
			// backupDbFilename should never be empty
			$this->backupDbFilename = $this->currentParams['backupDbFilename'];
			// backupFilesFilename should never etc.
			$this->backupFilesFilename = $this->currentParams['backupFilesFilename'];
		}

		$this->latestRootDir = $latest.DIRECTORY_SEPARATOR.'prestashop';
		$this->adminDir = str_replace($this->prodRootDir,'',$this->adminDir);
		// @TODO future option "install in test dir"
		//	$this->testRootDir = $this->autoupgradePath.DIRECTORY_SEPARATOR.'test';

		/* option */
		if (class_exists('Configuration',false))
		{
			$this->dontBackupImages = !Configuration::get('PS_AUTOUP_DONT_SAVE_IMAGES');
			$this->keepDefaultTheme = Configuration::get('PS_AUTOUP_KEEP_DEFAULT_THEME');
			$this->keepTrad = Configuration::get('PS_AUTOUP_KEEP_TRAD');
			$this->manualMode = Configuration::get('PS_AUTOUP_MANUAL_MODE');
			$this->desactivateCustomModule = Configuration::get('PS_AUTOUP_CUSTOM_MOD_DESACT');
		}
		else
		{
			$this->dontBackupImages = $this->currentParams['dontBackupImages'];
			$this->keepDefaultTheme = $this->currentParams['keepDefaultTheme'];
			$this->keepTrad = $this->currentParams['keepTrad'];
			$this->manualMode = $this->currentParams['manualMode'];
			$this->desactivateCustomModule = $this->currentParams['desactivateCustomModule'];
		}
		// We can add any file or directory in the exclude dir : theses files will be not removed or overwritten	
		// @TODO cache should be ignored recursively, but we have to reconstruct it after upgrade
		// - compiled from smarty
		// - .svn
		$this->backupIgnoreAbsoluteFiles[] = "/tools/smarty_v2/compile";
		$this->backupIgnoreAbsoluteFiles[] = "/tools/smarty_v2/cache";
		$this->backupIgnoreAbsoluteFiles[] = "/tools/smarty/compile";
		$this->backupIgnoreAbsoluteFiles[] = "/tools/smarty/cache";

		$this->excludeFilesFromUpgrade[] = '.';
		$this->excludeFilesFromUpgrade[] = '..';
		$this->excludeFilesFromUpgrade[] = '.svn';
		$this->excludeFilesFromUpgrade[] = 'install';
		$this->excludeFilesFromUpgrade[] = 'settings.inc.php';
		// this will exclude autoupgrade dir from admin, and autoupgrade from modules
		$this->excludeFilesFromUpgrade[] = 'autoupgrade';
		$this->backupIgnoreFiles[] = '.';
		$this->backupIgnoreFiles[] = '..';
		$this->backupIgnoreFiles[] = '.svn';
		$this->backupIgnoreFiles[] = 'autoupgrade';

		if ($this->dontBackupImages)
			$this->backupIgnoreAbsoluteFiles[] = "/img";


		if ($this->keepDefaultTheme)
			$this->excludeAbsoluteFilesFromUpgrade[] = "/themes/prestashop";

		if ($this->keepTrad)
			$this->excludeFilesFromUpgrade[] = "translations";
	}

	/**
	 * getFilePath return the path to the zipfile containing prestashop.
	 *
	 * @return void
	 */
	private function getFilePath()
	{
		return $this->autoupgradePath.DIRECTORY_SEPARATOR.$this->destDownloadFilename;
	}

	public function postProcess()
	{
		$this->_setFields();

		if (!empty($_POST))
			$this->_postConfig($this->_fieldsAutoUpgrade);
	}

	public function ajaxProcessUpgradeComplete()
	{
		$this->nextDesc = $this->l('Upgrade process done. Congratulations ! You can now reactive your shop.');
		$this->next = '';
	}
	public function ajaxProcessCheckFilesVersion()
	{
		$this->_loadDbRelatedClasses();
		$this->upgrader = new Upgrader();

		$changedFileList = $this->upgrader->getChangedFilesList();
		if ($this->upgrader->isAuthenticPrestashopVersion() == true
			&& !is_array($changedFileList) )
		{
			$this->nextParams['status'] = 'error';
			$this->nextParams['msg'] = '[TECHNICAL ERROR] Unable to check files';
			$testOrigCore = false;
		}
		else
		{
			if ($this->upgrader->isAuthenticPrestashopVersion() != false)
			{
				$this->nextParams['status'] = 'ok';
				$testOrigCore = true;
			}
			else
			{
				$testOrigCore = false;
				$this->nextParams['status'] = 'warn';
			}

			if (!isset($changedFileList['core']))
				$changedFileList['core'] = array();
			if (!isset($changedFileList['translation']))
				$changedFileList['translation'] = array();
			if (!isset($changedFileList['mail']))
				$changedFileList['mail'] = array();

			if ($changedFileList === false)
			{
				$changedFileList = array();
				$this->nextParams['msg'] = $this->l('Unable to check files');
				$this->nextParams['status'] = 'error';
			}
			else
			{
				$this->nextParams['msg'] = ($testOrigCore?$this->l('Core files are ok'):sprintf($this->l('%1$s core files have been modified (%2$s total)'), count($changedFileList['core']), count(array_merge($changedFileList['core'], $changedFileList['mail'], $changedFileList['translation']))));
			}
			$this->nextParams['result'] = $changedFileList;
		}
	}

	public function ajaxProcessUpgradeNow()
	{
		$this->nextDesc = $this->l('Starting upgrade ...');

		if ($this->useSvn)
		{
			$this->next = 'svnCheckout';
			$this->nextDesc = $this->l('switching to svn checkout (useSvn set to true)');
		}
		else
		{
			$this->next = 'download';
			$this->nextDesc = $this->l('Shop deactivated. Now downloading (this can takes some times )...');
		}
	}

	public function ajaxProcessSvnExport()
	{
		if ($this->useSvn)
		{
			// first of all, delete the content of the latest root dir just in case
			if (is_dir($this->latestRootDir))
				Tools::deleteDirectory($this->latestRootDir, false);

			if (!file_exists($this->latestRootDir))
			{
				@mkdir($this->latestRootDir);
			}

			if (svn_export($this->autoupgradePath . DIRECTORY_SEPARATOR . $this->svnDir, $this->latestRootDir))
			{

				// export means svn means install-dev and admin-dev.
				// let's rename admin to the correct admin dir
				// and rename install-dev to install
				$adminDir = str_replace($this->prodRootDir, '', $this->adminDir);
				rename($this->latestRootDir.DIRECTORY_SEPARATOR.'install-dev', $this->latestRootDir.DIRECTORY_SEPARATOR.'install');
				rename($this->latestRootDir.DIRECTORY_SEPARATOR.'admin-dev', $this->latestRootDir.DIRECTORY_SEPARATOR.$adminDir);

				// Unsetting to force listing
				unset($this->nextParams['removeList']);
				$this->next = "removeSamples";
				$this->nextDesc = $this->l('Export svn complete. removing sample files...');
				return true;
			}
			else
			{
				$this->next = 'error';
				$this->nextDesc = $this->l('error when svn export ');
			}
		}
	}

	/**
	 * extract last version into admin/autoupgrade/latest directory
	 * 
	 * @return void
	 */
	public function ajaxProcessUnzip(){
		if(version_compare(_PS_VERSION_,'1.4.5.0','<')
			AND !class_exists('Tools',false)
		)
			require_once('Tools.php');

		$filepath = $this->getFilePath();
		$destExtract = $this->autoupgradePath.DIRECTORY_SEPARATOR.'latest';
		if (file_exists($destExtract))
			Tools::deletedirectory($destExtract);

		if (self::ZipExtract($filepath,$destExtract))
		{
				$adminDir = str_replace($this->prodRootDir, '', $this->adminDir);
				rename($this->latestRootDir.DIRECTORY_SEPARATOR.'admin', $this->latestRootDir.DIRECTORY_SEPARATOR.$adminDir);
				// Unsetting to force listing
				unset($this->nextParams['removeList']);
				$this->next = "removeSamples";
				$this->nextDesc = $this->l('Extract complete. removing sample files...');
				return true;
		}
		else{
				$this->next = "error";
				$this->nextDesc = sprintf($this->l('unable to extract %1$s into %2$s ...'),$filepath,$destExtract);
				return true;
		}
	}


	/**
	 * _listSampleFiles will make a recursive call to scandir() function
	 * and list all file which match to the $fileext suffixe (this can be an extension or whole filename)
	 *
	 * @TODO maybe $regex instead of $fileext ?
	 * @param string $dir directory to look in
	 * @param string $fileext suffixe filename
	 * @return void
	 */
	private function _listSampleFiles($dir, $fileext = '.jpg'){
		$res = true;
		$dir = rtrim($dir,'/').DIRECTORY_SEPARATOR;

		$toDel = scandir($dir);
		// copied (and kind of) adapted from AdminImages.php
		foreach ($toDel AS $file)
		{
			if ($file!='.' AND $file != '..' AND $file != '.svn')
			{

				if (preg_match('#'.preg_quote($fileext,'#').'$#i',$file))
				{
					$this->sampleFileList[] = $dir.$file;
				}
				else if (is_dir($dir.$file))
				{
					$res &= $this->_listSampleFiles($dir.$file);
				}
			}
		}
		return $res;
	}

	public function _listFilesInDir($dir, $way = 'backup')
	{
		$list = array();
		$allFiles = scandir($dir);
		foreach ($allFiles as $file)
		{
			if ($file[0] != '.')
			{
				$fullPath = $dir.DIRECTORY_SEPARATOR.$file;

				if (!$this->_skipFile($file, $fullPath, $way))
				{
					if (is_dir($fullPath))
						$list = array_merge($list, $this->_listFilesInDir($fullPath, $way));
					else
						$list[] = $fullPath;
				}
				else
					$list[] = $fullPath;
			}
		}
		return $list;
	}

	public function _listFilesToUpgrade($dir)
	{
		static $list = array();
		$allFiles = scandir($dir);
		foreach ($allFiles as $file)
		{
			$fullPath = $dir.DIRECTORY_SEPARATOR.$file;

			if (!$this->_skipFile($file, $fullPath, "upgrade"))
			{
				$list[] = $fullPath;
				// if is_dir, we will create it :)
				if (is_dir($fullPath))
					if (strpos($dir.DIRECTORY_SEPARATOR.$file, 'install') === false)
						$this->_listFilesToUpgrade($fullPath);
			}
		}

		file_put_contents($this->autoupgradePath.DIRECTORY_SEPARATOR.$this->toUpgradeFileList,serialize($list));
		$this->nextParams['filesToUpgrade'] = $this->toUpgradeFileList;
	}


	public function ajaxProcessUpgradeFiles()
	{
		// @TODO :
		$this->nextParams = $this->currentParams;
		if (!isset($this->nextParams['filesToUpgrade']))
			$this->_listFilesToUpgrade($this->latestRootDir);

		// later we could choose between _PS_ROOT_DIR_ or _PS_TEST_DIR_
		$this->destUpgradePath = $this->prodRootDir;

		// upgrade files one by one like for the backup
		// with a 1000 loop because it's funny
		// @TODO :
		// foreach files in latest, copy
		$this->next = 'upgradeFiles';
		$filesToUpgrade = @unserialize(file_get_contents($this->nextParams['filesToUpgrade']));
		if (!is_array($filesToUpgrade))
		{
			$this->next = 'error';
			$this->nextDesc = $this->l('filesToUpgrade is not an array');
			$this->nextQuickInfo[] = $this->l('filesToUpgrade is not an array');
			return false;
		}

		// @TODO : does not upgrade files in modules, translations if they have not a correct md5 (or crc32, or whatever) from previous version
		for ($i=0;$i < self::$loopUpgradeFiles;$i++)
		{
			if (sizeof($filesToUpgrade)<=0)
			{
				$this->next = 'upgradeDb';
				unlink($this->nextParams['filesToUpgrade']);
				$this->nextDesc = $this->l('All files upgraded. Now upgrading database');
				$this->nextResponseType = 'xml';
				break;
			}

			$file = array_shift($filesToUpgrade);
			if (!$this->upgradeThisFile($file))
			{
				// put the file back to the begin of the list
				$totalFiles = array_unshift($filesToUpgrade,$file);
				$this->next = 'error';
				$this->nextQuickInfo[] = sprintf($this->l('error when trying to upgrade %s'),$file);
				break;
			}
			else{
				$this->nextQuickInfo[] = sprintf($this->l('copied %1$s. %2$s files left to upgrade.'),$file, sizeof($filesToUpgrade));
				// @TODO : maybe put several files at the same times ?
				$this->nextDesc = sprintf($this->l('%2$s files left to upgrade.'),$file,sizeof($filesToUpgrade));
			}
		}
		file_put_contents($this->nextParams['filesToUpgrade'],serialize($filesToUpgrade));
		return true;
	}

	public function _modelDo($method)
	{
		@set_time_limit(0);
		@ini_set('max_execution_time', '0');
		// setting the memory limit to 128M only if current is lower
		$memory_limit = ini_get('memory_limit');
		if (substr($memory_limit,-1) != 'G'
			AND ((substr($memory_limit,-1) == 'M' AND substr($memory_limit,0,-1) < 128)
			OR is_numeric($memory_limit) AND (intval($memory_limit) < 131072))
		){
			@ini_set('memory_limit','128M');
		}
		require_once($this->prodRootDir.'/config/autoload.php');

		/* Redefine REQUEST_URI if empty (on some webservers...) */
		if (!isset($_SERVER['REQUEST_URI']) || $_SERVER['REQUEST_URI'] == '')
			$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
		if ($tmp = strpos($_SERVER['REQUEST_URI'], '?'))
			$_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 0, $tmp);
		$_SERVER['REQUEST_URI'] = str_replace('//', '/', $_SERVER['REQUEST_URI']);

		define('INSTALL_VERSION', $this->currentParams['install_version']);
		define('INSTALL_PATH', realpath($this->latestRootDir.DIRECTORY_SEPARATOR.'install'));


		define('PS_INSTALLATION_IN_PROGRESS', true);
		require_once(INSTALL_PATH.'/classes/ToolsInstall.php');
		define('SETTINGS_FILE', $this->prodRootDir . '/config/settings.inc.php');
		define('DEFINES_FILE', $this->prodRootDir .'/config/defines.inc.php');
		define('INSTALLER__PS_BASE_URI', substr($_SERVER['REQUEST_URI'], 0, -1 * (strlen($_SERVER['REQUEST_URI']) - strrpos($_SERVER['REQUEST_URI'], '/')) - strlen(substr(dirname($_SERVER['REQUEST_URI']), strrpos(dirname($_SERVER['REQUEST_URI']), '/')+1))));
		define('INSTALLER__PS_BASE_URI_ABSOLUTE', 'http://'.ToolsInstall::getHttpHost(false, true).INSTALLER__PS_BASE_URI);

		// XML Header
		header('Content-Type: text/xml');

	// Switching method
		if (in_array($method, array('doUpgrade', 'createDB', 'checkShopInfos')))
		{
			global $logger;
			$logger = new FileLogger();
			$logger->setFilename($this->prodRootDir.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR.@date('Ymd').'_installation.log');
		}
		switch ($method)
		{
			case 'checkConfig' :
				require_once(INSTALL_PATH.DIRECTORY_SEPARATOR.'xml'.DIRECTORY_SEPARATOR.'checkConfig.php');
				die();
			break;

			case 'checkDB' :
				require_once(INSTALL_PATH.DIRECTORY_SEPARATOR.'xml'.DIRECTORY_SEPARATOR.'checkDB.php');
			break;

			case 'createDB' :
				require_once(INSTALL_PATH.DIRECTORY_SEPARATOR.'xml'.DIRECTORY_SEPARATOR.'createDB.php');
			break;

			case 'checkMail' :
				require_once(INSTALL_PATH.DIRECTORY_SEPARATOR.'xml'.DIRECTORY_SEPARATOR.'checkMail.php');
			break;

			case 'checkShopInfos' :
				require_once(INSTALL_PATH.DIRECTORY_SEPARATOR.'xml'.DIRECTORY_SEPARATOR.'checkShopInfos.php');
			break;

			case 'doUpgrade' :
				require_once(INSTALL_PATH.DIRECTORY_SEPARATOR.'xml'.DIRECTORY_SEPARATOR.'doUpgrade.php');
			break;

			case 'getVersionFromDb' :
				require_once(INSTALL_PATH.DIRECTORY_SEPARATOR.'xml'.DIRECTORY_SEPARATOR.'getVersionFromDb.php');
			break;
		}
	}
	public function ajaxProcessUpgradeDb()
	{

		// @TODO : 1/2/3 have to be done at the beginning !!!!!!!!!!!!!!!!!!!!!!
		$this->nextParams = $this->currentParams;

		// use something like actual in install-dev
		// Notice : xml used here ...
		if (!isset($this->currentParams['upgradeDbStep']))
		{
			$this->nextParams['upgradeDbStep'] = 1;
			$this->next = 'upgradeDb';
			$this->nextDesc = 'upgrading database';
			$this->nextResponseType = 'xml';
		}
		switch ($this->currentParams['upgradeDbStep'])
		{
			default:
			// 1) confirm version is correct(DB)
			// install/model.php?method=getVersionFromDb&language=0
			case '1':
				$this->_modelDo('getVersionFromDb');
				break;
			// 2) confirm config is correct (r/w rights)
			//	install/model.php?method=checkConfig&firsttime=0
			case '2':
				if(!$this->_modelDo('checkConfig'))
				{
					$this->next = 'error';
					$this->nextDesc = $this->l('error when checking configuration');
				}
				break;

			case '3':
				if (!$this->_modelDo('doUpgrade'))
				{
					$this->next = 'error';
					$this->nextDesc = $this->l('error during upgrade Db. You may need to restore your database');
				}
				break;
			case '4':
				$this->next = 'upgradeComplete';
				$this->nextResponseType = 'json';
				$this->nextDesc = $this->l('Way to go ! Upgrade complete. You can now reactivate your shop.');
				break;
		}

		// 5) compare activated modules and reactivate them
		// @TODO
		return true;

	}


	/**
	 * upgradeThisFile
	 *
	 * @param mixed $file
	 * @return void
	 */
	public function upgradeThisFile($file)
	{
		// @TODO : later, we could handle customization with some kind of diff functions
		// for now, just copy $file in str_replace($this->latestRootDir,_PS_ROOT_DIR_)
		// $file comes from scandir function, no need to lost time and memory with file_exists()
		if ($this->_skipFile('', $file,'upgrade'))
		{
			$this->nextQuickInfo[] = $this->l('%s ignored');
			return true;
		}
		else
		{
			$dest = str_replace($this->latestRootDir, $this->destUpgradePath,$file);

			if (is_dir($file))
			{
				// if $dest is not a directory (that can happen), just remove that file
				if (!is_dir($dest) AND file_exists($dest))
					unlink($dest);

				if (!file_exists($dest))
				{
					if (@mkdir($dest))
						return true;
					else
					{
						$this->next = 'error';
						$this->nextQuickInfo[] = sprintf($this->l('error when creating directory %s'), $dest);
						$this->nextDesc = sprintf($this->l('error when creating directory %s'), $dest);
						return false;
					}
				}
				else // directory already exists
					return true;
			}
			else
			{
				if (copy($file,$dest))
					return true;
				else
				{
					$this->next = 'error';
					$this->nextQuickInfo[] = sprintf($this->l('error for copy %1$s in %2$s'), $file, $dest);
					$this->nextDesc = sprintf($this->l('error for copy %1$s in %2$s'), $file, $dest);
					return false;
				}
			}
		}

	}

	public function ajaxProcessRollback()
	{
		// 1st, need to analyse what was wrong.

		$this->nextParams = $this->currentParams;
		if (!empty($this->backupFilesFilename) AND file_exists($this->backupFilesFilename))
		{
			$this->next = 'restoreFiles';
			$this->status = 'ok';
			$this->nextDesc = $this->l('Restoring files...');
		}
		else
		{
			if (!empty($this->backupDbFilename) AND file_exists($this->backupDbFilename))
			{
				$this->next = 'restoreDb';
				$this->status = 'ok';
				$this->nextDesc = $this->l('restoring Database ...');
			}
			else
			{
				// 2nd case if upgradeFiles made an error
				// 3rd case if no upgrade has been done
				// all theses cases are handled by the method ajaxRequestRollback()
				$this->next = ''; // next is empty : nothing next :)
				$this->status = 'ok';
				if (isset($this->currentParams['firstTime']))
						$this->nextDesc = $this->l('Nothing has to be restored');
					else
						$this->nextDesc = $this->l('All your site is restored... ');
			}
		}
	}

	/**
	 * ajaxProcessRestoreFiles restore the previously saved files, 
	 * and delete files that weren't archived
	 *
	 * @return boolean true if succeed
	 */
	public function ajaxProcessRestoreFiles()
	{
				$this->next = 'restoreFiles';
		// @TODO : workaround max_execution_time / ajax batch unzip
		// very first restoreFiles step : extract backup 
		if (!empty($this->backupFilesFilename) AND file_exists($this->backupFilesFilename))
		{
			// cleanup current PS tree
			$fromArchive = $this->_listArchivedFiles();
			file_put_contents($this->autoupgradePath.DIRECTORY_SEPARATOR.$this->fromArchiveFileList, serialize($fromArchive));
	
			//$this->_cleanUp($this->prodRootDir.'/');
			$this->nextQuickInfo[] = $this->l('root directory cleaned.');

			$filepath = $this->backupFilesFilename;
			$destExtract = $this->prodRootDir;

			if (self::ZipExtract($filepath, $destExtract))
			{
				$this->next = 'restoreFiles';
				// get new file list 
				$this->nextDesc = $this->l('Files restored. Removing files added by upgrade ...');
				// once it's restored, do not delete the archive file. This has to be done manually
				// but we can empty the var, to avoid loop.
				$this->backupFilesFilename = '';
				return true;
			}
			else
			{
				$this->next = "error";
				$this->nextDesc = sprintf($this->l('unable to extract $1$s into %2$s .'), $filepath, $destExtract);
				return false;
			}
		}
		
		// very second restoreFiles step : remove new files that shouldn't be there
		// for that, we will make a diff between the current filelist in root dir 
		// and the archive file list we previously saved
		// files to remove : differences between complete list and archive list
		if (!file_exists($this->autoupgradePath.DIRECTORY_SEPARATOR.$this->toRemoveFileList))
		{
			if (!isset($fromArchive))
				$fromArchive = unserialize(file_get_contents($this->fromArchiveFileList));
			$toRemove = array_diff($this->_listFilesInDir($this->prodRootDir), $fromArchive);
			file_put_contents($this->autoupgradePath.DIRECTORY_SEPARATOR.$this->toRemoveFileList,serialize($toRemove));
		}
		
		if (!isset($toRemove))
			$toRemove = unserialize(file_get_contents($this->toRemoveFileList));

		for($i=0;$i<self::$loopRemoveUpgradedFiles ;$i++)
		{
			if (count($toRemove)<=0)
			{
				$this->stepok = true;
				$this->status = 'ok';
				$this->next = 'rollback';
				$this->nextDesc = $this->l('Files from upgrade has been removed.');
				$this->nextQuickInfo[] = $this->l('files from upgrade has been removed.');
				break;
			}
			else
			{
				$checkFile = array_shift($toRemove);
				// 
				if (in_array($checkFile, $toRemove) 
					&& !$this->_skipFile('', $path.$file, 'backup')
					&& !$this->_skipFile('', $path.$file, 'upgrade')
				)
				{
					if (file_exists($file) && @unlink($file))
					{
						$this->nextQuickInfo[] = sprintf($this->l('%s removed'), $file);
					}
					else
					{
						$this->next = 'error';
						$this->nextDesc = sprintf($this->l('error when removing %1$s'), $file);
						$this->nextQuickInfo[] = sprintf($this->l('%s not removed'), $file);
						return false;
					}
				}
			}
		}
		file_put_contents($this->autoupgradePath.DIRECTORY_SEPARATOR.$this->toRemoveFileList,serialize($toRemove));
		$this->nextDesc = sprintf($this->l('%s left to remove'), count($toRemove));
				$checkFile = array_shift($toRemove);
		return true;
	}

	/**
	* try to restore db backup file
	* @return type : hey , what you expect ? well mysql errors array .....
	* @TODO : maybe this could be in the Backup class
	*/
	public function ajaxProcessRestoreDb()
	{
		$this->_loadDbRelatedClasses();

		$exts = explode('.', $this->backupDbFilename);
		$fileext = $exts[count($exts)-1];
		$requests = array();
		$errors = array();
		$content = '';
		switch ($fileext)
		{
			case 'bz':
			case 'bz2':
				if ($fp = bzopen($this->backupDbFilename, 'r'))
				{
					while(!feof($fp))
						$content .= bzread($fp, filesize($this->backupDbFilename));
					bzclose($fp);
				}
				break;
			case 'gz':
				if ($fp = gzopen($this->backupDbFilename, 'r'))
				{
					while(!feof($fp))
						$content = gzread($fp, filesize($this->backupDbFilename));
					gzclose($fp);
				}
				break;
			// default means sql ?
			default :
				if ($fp = fopen($this->backupDbFilename, 'r'))
				{
					while(!feof($fp))
						$content = fread($fp, filesize($this->backupDbFilename));
						fclose($fp);
				}
		}

		if ($content=='')
			return false;

		// preg_match_all is better than preg_split (what is used in doUpgrade.php)
		// This way we avoid extra blank lines
		// option s (PCRE_DOTALL) added
		// @TODO need to check if a ";" in description could block that (I suppose it can at the end of a line)
		preg_match_all('/(.*;)[\n\r]+/Usm', $content, $requests);
		/* @TODO maybe improve regex pattern ... */
		$db = Db::getInstance();
		if (count($requests[0])>0)
		{
			foreach ($requests[1] as $request)
				if (!empty($request))
					if (!$db->Execute($request))
						$this->nextQuickInfo[] = $db->getMsgError();

			// once it's restored, delete the file for that "session"
			$this->backupDbFilename = '';
			// unlink($this->backupDbFilename);
			// Configuration::updateValue('UPGRADER_BACKUPDB_FILENAME','');
		}
		else
			$this->nextQuickInfo[] = $this->l('Nothing to restore (no request found)');

		$this->next = 'rollback';
		$this->nextDesc = 'Database restore done.';
	}

	private function _loadDbRelatedClasses()
	{
		// Manual inclusion of all classes used
		if(!class_exists('ObjectModel',false))
		{
			require_once(_PS_ROOT_DIR_.'/classes/ObjectModel.php');
			if(!class_exists('ObjectModel',false))
				eval('Class ObjectModel extends ObjectModelCore{}');
		}

		if(!class_exists('Language',false))
		{
			require_once(_PS_ROOT_DIR_.'/classes/Language.php');
			if(!class_exists('Language',false))
				eval('Class Language extends LanguageCore{}');
		}
		if(!class_exists('Db',false))
		{
			require_once(_PS_ROOT_DIR_.'/classes/Db.php');
			if(!class_exists('Db',false))
				eval('abstract Class Db extends DbCore{}');
		}
		if(!class_exists('MySQL',false))
		{
			require_once(_PS_ROOT_DIR_.'/classes/MySQL.php');
			if(!class_exists('MySQL',false))
				eval('Class MySQL extends MySQLCore{}');
		}

		if(!class_exists('Validate',false))
		{
			require_once(_PS_ROOT_DIR_.'/classes/Validate.php');
			if(!class_exists('Validate',false))
				eval('Class Validate extends ValidateCore{}');
		}
		if(!class_exists('Configuration',false))
		{
			require_once(_PS_ROOT_DIR_.'/classes/Configuration.php');
			if(!class_exists('Configuration',false))
				eval('Class Configuration extends ConfigurationCore{}');
		}
		if (!class_exists('Backup',false))
		{
			if (!class_exists('BackupCore', false))
				require_once('Backup.php');
			if(file_exists(_PS_ROOT_DIR_.'/override/classes/Backup.php'))
				require_once(_PS_ROOT_DIR_.'/override/classes/Backup.php');
			else
				eval('Class Backup extends BackupCore{}');
		}

		if (!defined('_PS_MAGIC_QUOTES_GPC_'))
			define('_PS_MAGIC_QUOTES_GPC_', get_magic_quotes_gpc());
		if (!defined('_PS_MYSQL_REAL_ESCAPE_STRING_'))
			define('_PS_MYSQL_REAL_ESCAPE_STRING_', function_exists('mysql_real_escape_string'));
		Configuration::loadConfiguration();
	}
	public function ajaxProcessBackupDb()
	{
		$this->_loadDbRelatedClasses();

		$backup = new Backup();
		// for backup db, use autoupgrade directory
		// @TODO : autoupgrade must not be static
		$backup->setCustomBackupPath('autoupgrade');
		// maybe for big tables we should save them in more than one file ?
		$res = $backup->add();
		if ($res)
		{
			$this->nextParams['backupDbFilename'] = $backup->id;
			// We need to load configuration to use it ...
			Configuration::loadConfiguration();
			Configuration::updateValue('UPGRADER_BACKUPDB_FILENAME', $backup->id);

			$this->next = 'upgradeFiles';
			$this->nextDesc = sprintf($this->l('Database backup done in %s. Now updating files'),$backup->id);
		}
		// if an error occur, we assume the file is not saved
	}

	public function ajaxProcessBackupFiles()
	{
		$this->nextParams = $this->currentParams;
		$this->stepDone = false;
		/////////////////////

		if (!isset($this->nextParams['filesForBackup']))
		{
			$list = $this->_listFilesInDir($this->prodRootDir);
			file_put_contents($this->autoupgradePath.DIRECTORY_SEPARATOR.$this->toBackupFileList,serialize($list));

			$this->nextQuickInfo[] = sprintf($this->l('%s Files to backup.'), sizeof($this->toBackupFileList));
			$this->nextParams['filesForBackup'] = $this->toBackupFileList;

			// delete old backup, create new
			if (file_exists($this->backupFilesFilename))
				unlink($this->backupFilesFilename);

			$this->nextQuickInfo[]	= sprintf($this->l('backup files initialized in %s'), $this->backupFilesFilename);
		}
		$filesToBackup = unserialize(file_get_contents($this->toBackupFileList));

		/////////////////////
		$this->next = 'backupFiles';
		// @TODO : display % instead of this
		$this->nextDesc = sprintf($this->l('Backup files in progress. %s files left'), sizeof($filesToBackup));
		if (is_array($filesToBackup))
		{
			// @TODO later
			// 1) calculate crc32 or md5 of next file
			// 2) use the provided xml with crc32 calculated from previous versions ?
			// or simply use the latest dir ?
			//$current = crc32(file_get_contents($file));
			//$file = $this->nextParams['filesForBackup'][0];
			//$latestFile = str_replace(_PS_ROOT_DIR_,$this->latestRootDir,$file);

			//	if (file_exists($latestFile))
			//		$latest = crc32($latestFile);
			//	else
			//		$latest = '';

			if (!self::$force_pclZip && class_exists('ZipArchive', false))
			{
				$zip = new ZipArchive();
				$zip->open($this->backupFilesFilename, ZIPARCHIVE::CREATE);
				$zip_add_method = 'addFile';
				$zip_close_method = 'close';
			}
			else
			{
				if (!class_exists('PclZip',false))
					require_once(dirname(__FILE__).'/pclzip.lib.php');
				$zip = new PclZip($this->backupFilesFilename);
				$zip->create($this->backupFilesFilename);
				$zip_add_method = 'add';
				$zip_close_method = 'privCloseFd';
			}
			if ($zip)
			{
				$this->next = 'backupFiles';
				// @TODO all in one time will be probably too long
				// 1000 ok during test, but 10 by 10 to be sure
				$this->stepok = false;
				// @TODO min(self::$loopBackupFiles, sizeof())
				for($i=0;$i<self::$loopBackupFiles;$i++)
				{
					if (sizeof($filesToBackup)<=0)
					{
						$this->stepok = true;
						$this->status = 'ok';
						$this->next = 'backupDb';
						$this->nextDesc = $this->l('All files saved. Now backup Database');
						$this->nextQuickInfo[] = $this->l('all files have been added to archive.');
						break;
					}
					// filesForBackup already contains all the correct files
					$file = array_shift($filesToBackup);
					$archiveFilename = ltrim(str_replace($this->prodRootDir,'',$file),DIRECTORY_SEPARATOR);
					// @TODO : maybe put several files at the same times ?
					if ($zip->{$zip_add_method}($file,$archiveFilename))
						$this->nextQuickInfo[] = sprintf($this->l('%1$s added to archive. %2$s left.'),$file, sizeof($filesToBackup));
					else
					{
					// if an error occur, it's more safe to delete the corrupted backup
						if (file_exists($this->backupFilesFilename))
							unlink($this->backupFilesFilename);
						$this->next = 'error';
						$this->nextDesc = sprintf($this->l('error when trying to add %1$s to archive %2$s.'),$file, $backupFilePath);
						break;
					}
				}

				$zip->$zip_close_method();
				file_put_contents($this->autoupgradePath.DIRECTORY_SEPARATOR.$this->toBackupFileList,serialize($filesToBackup));
				return true;
			}
			else{
				$this->next = 'error';
				$this->nextDesc = $this->l('unable to open archive');
				return false;
			}
		}
		else
		{
			$this->next = 'backupDb';
			$this->nextDesc = 'All files saved. Now backup Database';
			return true;
		}
		// 4) save for display.
	}


	private function _removeOneSample($removeList)
	{
		if (is_array($removeList) AND sizeof($removeList)>0)
		{
			if (file_exists($removeList[0]) AND unlink($removeList[0]))
			{
				$item = array_shift($removeList);
				$this->next = 'removeSamples';
				$this->nextParams['removeList'] = $removeList;
				$this->nextQuickInfo[] = sprintf($this->l('%1$s removed. %2$s items left'), $item, sizeof($removeList));
			}
			else
			{
				$this->next = 'error';
				$this->nextParams['removeList'] = $removeList;
				$this->nextQuickInfo[] = sprintf($this->l('error when removing %1$s, %2$s items left'), $removeList[0], sizeof($removeList));
				return false;
			}
		}
		return true;
	}

	public function ajaxProcessRemoveSamples(){
		$this->stepDone = false;
		// all images from img dir exept admin ?
		// all images like logo, favicon, ?.
		// all custom image from modules ?
		// all custom image from theme ?
		if (!isset($this->currentParams['removeList']))
		{
			$this->_listSampleFiles($this->autoupgradePath.'/latest/prestashop/img', 'jpg');
			$this->_listSampleFiles($this->autoupgradePath.'/latest/prestashop/modules/editorial/', 'homepage_logo.jpg');
			// @TODO handle this bad thing
			$this->nextQuickInfo[] = sprintf($this->l('Starting to remove %1$s sample files'), sizeof($this->sampleFileList));
			$this->nextParams['removeList'] = $this->sampleFileList;
		}


		// @TODO : removing @, adding if file_exists
//		@unlink(_PS_ROOT_DIR_.'modules'.DIRECTORY_SEPARATOR.'editorial'.DIRECTORY_SEPARATOR.'editorial.xml');
//		@unlink(_PS_ROOT_DIR_.'modules'.DIRECTORY_SEPARATOR.'editorial'.DIRECTORY_SEPARATOR.'homepage_logo.jpg'); // homepage custom ?
//		@unlink(_PS_ROOT_DIR_.'img'.DIRECTORY_SEPARATOR.'logo.jpg');
//		@unlink(_PS_ROOT_DIR_.'img'.DIRECTORY_SEPARATOR.'favicon.ico');
		$resRemove = true;
		for($i=0;$i<self::$loopRemoveSamples;$i++)
		{
			if (sizeof($this->nextParams['removeList']) <= 0 )
			{
				$this->stepDone = true;
				$this->next = 'backupFiles';
				$this->nextDesc = $this->l('All sample files removed. Now backup files.');
				// break the loop, all sample already removed
				return true;
			}
			$resRemove &= $this->_removeOneSample($this->nextParams['removeList']);
			if (!$resRemove)
				break;
		}

		return $resRemove;
	}

	public function ajaxProcessSvnCheckout()
	{
		$this->nextParams = $this->currentParams;
		if ($this->useSvn){
			$dest = $this->autoupgradePath . DIRECTORY_SEPARATOR . $this->svnDir;

			$svnStatus = svn_status($dest);
			if (is_array($svnStatus))
			{
				if (sizeof($svnStatus) == 0)
				{
					$this->next = 'svnExport';
					$this->nextDesc = sprintf($this->l('working copy already %s up-to-date. now exporting it into latest dir'),$dest);
				}
				else
				{
					// we assume no modification has been done
					// @TODO a svn revert ?
					if ($svnUpdate = svn_update($dest))
					{
						$this->next = 'svnExport';
						$this->nextDesc = sprintf($this->l('SVN Update done for working copy %s . now exporting it into latest...'),$dest);
					}
				}
			}
			else
			{
					// no valid status found
					// @TODO : is 0777 good idea ?
					if (!file_exists($dest))
						if (!@mkdir($dest,0777))
						{
							$this->next = 'error';
							$this->nextDesc = sprintf($this->l('unable to create directory %s'),$dest);
							return false;
						}

					if (svn_checkout($this->svn_link, $dest))
					{
						$this->next = 'svnExport';
						$this->nextDesc = sprintf($this->l('SVN Checkout done from %s . now exporting it into latest...'),$this->svn_link);
						return true;
					}
					else
					{
						$this->next = 'error';
						$this->nextDesc = $this->l('SVN Checkout error...');
					}
				}
		}
		else
		{
			$this->next = 'error';
			$this->nextDesc = $this->l('not allowed to use svn');
		}
	}

	public function ajaxProcessDownload()
	{
		if (@ini_get('allow_url_fopen'))
		{
			$res = $this->upgrader->downloadLast($this->autoupgradePath,$this->destDownloadFilename);
			if ($res){
			 	if (md5_file(realpath($this->autoupgradePath).DIRECTORY_SEPARATOR.$this->destDownloadFilename) == $this->upgrader->md5 )
				{
					$this->next = 'unzip';
					$this->nextDesc = $this->l('Download complete. Now extracting');
				}
				else
				{
					$this->next = 'error';
					$this->nextDesc = $this->l('Download complete but md5sum does not match. Operation aborted.');
				}
			}
			else
			{
				$this->next = 'error';
				$this->nextDesc = $this->l('Error during download');
			}
		}
		else
		{
			// @TODO : ftp mode
			$this->next = 'error';
			$this->nextDesc = sprintf($this->l('you need allow_url_fopen for automatic download. You can also manually upload it in %s'),$this->autoupgradePath.$this->destDownloadFilename);
		}
	}

	public function buildAjaxResult()
	{
		$return['error'] = $this->error;
		$return['stepDone'] = $this->stepDone;
		$return['next'] = $this->next;
		$return['status'] = $this->next == 'error' ? 'error' : 'ok';
		$return['nextDesc'] = $this->nextDesc;

		$return['upgradeDbStep'] = 0;
		foreach($this->ajaxParams as $v)
			if(property_exists($this,$v))
				$this->nextParams[$v] = $this->$v;

		$return['nextParams'] = $this->nextParams;
		if (!isset($return['nextParams']['upgradeDbStep']))
			$return['nextParams']['upgradeDbStep'] = 0;

		$return['nextParams']['typeResult'] = $this->nextResponseType;

		$return['nextQuickInfo'] = $this->nextQuickInfo;
		return Tools14::jsonEncode($return);
	}

	/**
	 * displayConf
	 *
	 * @return void
	 */
	public function displayConf()
	{

		if (version_compare(_PS_VERSION_,'1.4.5.0','<') AND false)
			$this->_errors[] = Tools::displayError('This class depends of several files modified in 1.4.5.0 version and should not be used in an older version');
		parent::displayConf();
	}

	public function ajaxPreProcess()
	{
		/* PrestaShop demo mode */
		if (defined('_PS_MODE_DEMO_') && _PS_MODE_DEMO_)
			return;
		/* PrestaShop demo mode*/
		
		if (!empty($_POST['responseType']) AND $_POST['responseType'] == 'json')
			header('Content-Type: application/json');

		if(!empty($_POST['action']))
		{
			$action = $_POST['action'];
			if (isset(self::$skipAction[strtolower($action)]))
			{
				$this->next = self::$skipAction[$action];
				$this->nextDesc = sprintf($this->l('action %s skipped'),$action);
				$this->nextQuickInfo[] = sprintf($this->l('action %s skipped'),$action);
				unset($_POST['action']);
			}
			else if (!method_exists(get_class($this), 'ajaxProcess'.$action))
			{
				$this->nextDesc = sprintf($this->l('action "%1$s" not found'), $action);
				$this->next = 'error';
				$this->error = '1';
			}
		}

		if (!method_exists('Tools', 'apacheModExists') || Tools::apacheModExists('evasive'))
			sleep(1);
	}

	private function _getJsErrorMsgs()
	{
		$INSTALL_VERSION = $this->currentParams['install_version'];
		$ret = '
var txtError = new Array();
txtError[0] = "'.$this->l('Required field').'";
txtError[1] = "'.$this->l('Too long!').'";
txtError[2] = "'.$this->l('Fields are different!').'";
txtError[3] = "'.$this->l('This email adress is wrong!').'";
txtError[4] = "'.$this->l('Impossible to send the email!').'";
txtError[5] = "'.$this->l('Can\'t create settings file, if /config/settings.inc.php exists, please give the public write permissions to this file, else please create a file named settings.inc.php in config directory.').'";
txtError[6] = "'.$this->l('Can\'t write settings file, please create a file named settings.inc.php in config directory.').'";
txtError[7] = "'.$this->l('Impossible to upload the file!').'";
txtError[8] = "'.$this->l('Data integrity is not valided. Hack attempt?').'";
txtError[9] = "'.$this->l('Impossible to read the content of a MySQL content file.').'";
txtError[10] = "'.$this->l('Impossible the access the a MySQL content file.').'";
txtError[11] = "'.$this->l('Error while inserting data in the database:').'";
txtError[12] = "'.$this->l('The password is incorrect (alphanumeric string at least 8 characters).').'";
txtError[14] = "'.$this->l('A Prestashop database already exists, please drop it or change the prefix.').'";
txtError[15] = "'.$this->l('This is not a valid file name.').'";
txtError[16] = "'.$this->l('This is not a valid image file.').'";
txtError[17] = "'.$this->l('Error while creating the /config/settings.inc.php file.').'";
txtError[18] = "'.$this->l('Error:').'";
txtError[19] = "'.$this->l('This PrestaShop database already exists. Please revalidate your authentication informations to the database.').'";
txtError[22] = "'.$this->l('An error occurred while resizing the picture.').'";
txtError[23] = "'.$this->l('Database connection is available!').'";
txtError[24] = "'.$this->l('Database Server is available but database is not found').'";
txtError[25] = "'.$this->l('Database Server is not found. Please verify the login, password and server fields.').'";
txtError[26] = "'.$this->l('An error occurred while sending email, please verify your parameters.').'";
txtError[37] = "'.$this->l('Impossible to write the image /img/logo.jpg. If this image already exists, please delete it.').'";
txtError[38] = "'.$this->l('The uploaded file exceeds the upload_max_filesize directive in php.ini').'";
txtError[39] = "'.$this->l('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form').'";
txtError[40] = "'.$this->l('The uploaded file was only partially uploaded').'";
txtError[41] = "'.$this->l('No file was uploaded.').'";
txtError[42] = "'.$this->l('Missing a temporary folder').'";
txtError[43] = "'.$this->l('Failed to write file to disk').'";
txtError[44] = "'.$this->l('File upload stopped by extension').'";
txtError[45] = "'.$this->l('Cannot convert your database\'s data to utf-8.').'";
txtError[46] = "'.$this->l('Invalid shop name').'";
txtError[47] = "'.$this->l('Your firstname contains some invalid characters').'";
txtError[48] = "'.$this->l('Your lastname contains some invalid characters').'";
txtError[49] = "'.$this->l('Your database server does not support the utf-8 charset.').'";
txtError[50] = "'.$this->l('Your MySQL server doesn\'t support this engine, please use another one like MyISAM').'";
txtError[51] = "'.$this->l('The file /img/logo.jpg is not writable, please CHMOD 755 this file or CHMOD 777').'";
txtError[52] = "'.$this->l('Invalid catalog mode').'";
txtError[999] = "'.$this->l('No error code available').'";
//upgrader
txtError[27] = "'.$this->l('This installer is too old.').'";
txtError[28] = "'.sprintf($this->l('You already have the %s version.'),$INSTALL_VERSION).'";
txtError[29] = "'.$this->l('There is no older version. Did you delete or rename the config/settings.inc.php file?').'";
txtError[30] = "'.$this->l('The config/settings.inc.php file was not found. Did you delete or rename this file?').'";
txtError[31] = "'.$this->l('Can\'t find the sql upgrade files. Please verify that the /install/sql/upgrade folder is not empty)').'";
txtError[32] = "'.$this->l('No upgrade is possible.').'";
txtError[33] = "'.$this->l('Error while loading sql upgrade file.').'";
txtError[34] = "'.$this->l('Error while inserting content into the database').'";
txtError[35] = "'.$this->l('Unfortunately,').'";
txtError[36] = "'.$this->l('SQL errors have occurred.').'";
txtError[37] = "'.$this->l('The config/defines.inc.php file was not found. Where did you move it?').'";';
		return $ret;
	}

	public function displayAjax(){
		echo $this->buildAjaxResult();
	}

	private function _displayRollbackForm()
	{
		echo '<fieldset><legend>'.$this->l('Rollback').'</legend>
		<div id="rollbackForm">';
		echo '<p>'
		.$this->l('After upgrading your shop, you can rollback to the previously database and files. Use this function if your theme or an essential module is not working correctly.')
		.'</p><br/>';

		if (empty($this->backupFilesFilename) AND empty($this->backupDbFilename))
			echo $this->l('No rollback available');
		else if (!empty($this->backupFilesFilename) OR !empty($this->backupDbFilename))
		{
			echo '<div id="rollbackContainer"><a class="upgradestep button" href="" id="rollback">'.$this->l('rollback').'</a></div><br/>';
		}
		if (!empty($this->backupFilesFilename) AND file_exists($this->backupFilesFilename))
			echo '<div id="restoreFilesContainer"><a href="" class="upgradestep button" id="restoreFiles">restoreFiles</a> '.sprintf($this->l('click to restore %s'),$this->backupFilesFilename).'</div><br/>';
		if (!empty($this->backupDbFilename) AND file_exists($this->backupDbFilename))
			echo '<div id="restoreDbContainer"><a href="" class="upgradestep button" id="restoreDb">restoreDb</a> '.sprintf($this->l('click to restore %s'), $this->backupDbFilename).'</div><br/>';

		echo '</div></fieldset>';
	}

	/** this returns fieldset containing the configuration points you need to use autoupgrade
	 * @return string 
	 */
	private function getCurrentConfiguration()
	{
		$content = '<fieldset class="width autoupgrade " >';
		$content .= '<legend><a href="#" id="currentConfigurationToggle">'.$this->l('Your current configuration').'</a></legend>';
		$content .= '<div id="currentConfiguration">
		<p>'.$this->l('All the following points must be ok in order to allow the upgrade.').'</p>
		<b>'.$this->l('Root directory').' : </b>'.$this->prodRootDir.'<br/><br/>';

		if ($this->checkAutoupgradeLastVersion())
			$srcModuleVersion = '../img/admin/enabled.gif';
		else
			$srcModuleVersion = '../img/admin/disabled.gif';

		if ($module_version = simplexml_load_file(dirname(__FILE__).'/config.xml'))
			$module_version = (string)$module_version->version;

		$content .= '<b>'.$this->l('Module version').' : </b>'
			.'<img src="'.$srcModuleVersion.'" /> ';
			if($this->lastAutoupgradeVersion)
				$content .= sprintf($this->l('Your version is up-to-date (%s)'), $module_version, $this->upgrader->autoupgrade_last_version).'<br/><br/>';
			else
			{
				$token_modules = Tools14::getAdminTokenLite('AdminModules');
				$content .= sprintf($this->l('Module version is outdated ( %1$s ). Please install the last version (%2$s)'), $module_version, $this->upgrader->autoupgrade_last_version);
				$content .= '<br/><br/><a class="button" href="index.php?tab=AdminModules&amp;'.$token_modules.'&amp;url='.$this->upgrader->autoupgrade_module_link.'">'.$this->l('Install the latest by clicking "Add from my computer"').'</a><br/><br/>' ;
			}

		if ($this->rootWritable)
			$srcRootWritable = '../img/admin/enabled.gif';
		else
			$srcRootWritable = '../img/admin/disabled.gif';
		$content .= '<b>'.$this->l('Root directory status').' : </b>'.'<img src="'.$srcRootWritable.'" /> '.($this->rootWritable?$this->l('fully writable'):$this->l('not writable recursively')).'<br/><br/>';
		
		if ($this->upgrader->need_upgrade)
		{
			if ($this->upgrader->autoupgrade)
				$srcAutoupgrade = '../img/admin/enabled.gif';
			else
				$srcAutoupgrade = '../img/admin/disabled.gif';

			$content .= '<b>'.$this->l('Upgrade available').' : </b>'.'<img src="'.$srcAutoupgrade.'" /> '.($this->upgrader->autoupgrade?$this->l('This release allows autoupgrade.'):$this->l('This release does not allow autoupgrade')).' <br/><br/>';
		}
		else
		{
			$srcAutoupgrade = '../img/admin/disabled.gif';
			$content .= '<b>'.$this->l('Upgrade available').' : </b>'.'<img src="../img/admin/disabled.gif" />'.$this->l('You already have the last version.').'<br/><br/>';
		}

		if (Configuration::get('PS_SHOP_ENABLE'))
		{
			$srcShopStatus = '../img/admin/disabled.gif';
			$label = $this->l('No');
		}
		else
		{
			$srcShopStatus = '../img/admin/enabled.gif';
			$label = $this->l('Yes');
		}
		if (method_exists('Tools','getAdminTokenLite'))
			$token_preferences = Tools::getAdminTokenLite('AdminPreferences');
		else
			$token_preferences = Tools14::getAdminTokenLite('AdminPreferences');

		$content .= '<b>'.$this->l('Shop deactivated').' : </b>'.'<img src="'.$srcShopStatus.'" /><a href="index.php?tab=AdminPreferences&token='.$token_preferences.'" class="button">'.$label.'</a><br/><br/>';
		$max_exec_time = ini_get('max_execution_time');
		if ($max_exec_time == 0)
			$srcExecTime = '../img/admin/enabled.gif';
		else
			$srcExecTime = '../img/admin/warning.gif';
		$content .= '<b>'.$this->l('PHP time limit').' : </b>'.'<img src="'.$srcExecTime.'" />'.($max_exec_time == 0?$this->l('disabled'):$max_exec_time.' '.$this->l('seconds')).' <br/><br/>';

		if ($testConfigDone = (Configuration::get('PS_AUTOUP_DONT_SAVE_IMAGES') !== false))
			$configurationDone = '../img/admin/enabled.gif';
		else
			$configurationDone = '../img/admin/disabled.gif';
		$content .= '<b>'.$this->l('Options chosen').' : </b>'.'<img src="'.$configurationDone.'" /> 
		<a class="button" id="scrollToOptions" href="#options">'
		.($testConfigDone
			?$this->l('autoupgrade configuration ok').' - '.$this->l('Modify your options')
			:$this->l('Please configure autoupgrade options')
		).'</a><br/><br/>';
		$content .= '</div></fieldset>';

		return $content;
	}

	private function _displayUpgraderForm()
	{
		global $cookie;
		$content = '';
		$pleaseUpdate = $this->upgrader->checkPSVersion();

		$content .= $this->getCurrentConfiguration();
		$content .= '<br/>';

		$content .= '<fieldset class=""><legend>'.$this->l('Update').'</legend>';

		$content .= '<b>'.$this->l('PrestaShop Original version').' : </b>'.'<span id="checkPrestaShopFilesVersion">
		<img id="pleaseWait" src="'.__PS_BASE_URI__.'img/loader.gif"/>
		</span>';
		$content .= '<script type="text/javascript">
			$("#currentConfigurationToggle").click(function(e){e.preventDefault();$("#currentConfiguration").toggle()});'
			.($this->configOk()?'$("#currentConfiguration").hide();$("#currentConfigurationToggle").after("<img src=\"../img/admin/enabled.gif\" />");':'').'</script>';
		$content .= '<div style="clear:left">&nbsp;</div><div style="float:left">
		<h1>'.sprintf($this->l('Your current prestashop version : %s '),_PS_VERSION_).'</h1>';
		$content .= '<p>'.sprintf($this->l('Last version is %1$s (%2$s) '), $this->upgrader->version_name, $this->upgrader->version_num).'</p>';

		// @TODO : this should be checked when init()
		if ($this->isUpgradeAllowed()) {
			if ($pleaseUpdate) {
				$content .= '<li><img src="'._PS_ADMIN_IMG_.'information.png" alt="information"/> '.$this->l('Latest Prestashop version available is:').' <b>'.$pleaseUpdate['name'].'</b></li>';
			}
//			echo '<input class="button" type="submit" name="sumbitUpdateVersion" value="'.$this->l('Backup Database, backup files and update right now and in one click !').'"/>';
//			echo '<input class="button" type="submit" id="refreshCurrent" value="'.$this->l("refresh update dir / current").'"/>';
			$content .= '<br/>';
		if ($this->upgrader->need_upgrade)
		{
			if($this->configOk())
			{
				$content .= '<p><a href="" id="upgradeNow" class="button-autoupgrade upgradestep">'.$this->l('Upgrade PrestaShop now !').'</a></p>';
				$content .= '<small>'.sprintf($this->l('PrestaShop will be downloaded from %s'), $this->upgrader->link).'</small><br/>';
				$content .= '<small><a href="'.$this->upgrader->changelog.'">'.$this->l('see CHANGELOG').'</a></small>';
			}
			else
				$content .= $this->displayWarning('Your current configuration does not allow upgrade.');
		}
		else
		{
			$content .= '<span class="button-autoupgrade upgradestep" >'.$this->l('Your shop is already up to date.').'</span> ';
		}
		$content .= '<br/><br/><small>'.sprintf($this->l('last datetime check : %s '),date('Y-m-d H:i:s',Configuration::get('PS_LAST_VERSION_CHECK'))).'</span> 
		<a class="button" href="index.php?tab=AdminSelfUpgrade&token='.Tools::getAdminToken('AdminSelfUpgrade'.(int)(Tab::getIdFromClassName(get_class($this))).(int)$cookie->id_employee).'&refreshCurrentVersion=1">'.$this->l('Please click to refresh').'</a>
		</small>';
	
		$content .= '</div>
		<div id="currentlyProcessing" style="display:none;float:right"><h4>Currently processing <img id="pleaseWait" src="'.__PS_BASE_URI__.'img/loader.gif"/></h4>

		<div id="infoStep" class="processing" style=height:50px;width:400px;" >'.$this->l('I\'m waiting for your command, sir').'</div>';
		$content .= '</div>';

		$content .= '</fieldset>';


			if (defined('_PS_MODE_DEV_') AND _PS_MODE_DEV_ AND $this->manualMode)
			{
				$content .= '<br class="clear"/>';
				$content .= '<fieldset class="autoupgradeSteps"><legend>'.$this->l('Step').'</legend>';
				$content .= '<h4>'.$this->l('Upgrade steps').'</h4>';
				$content .= '<div>';
				$content .= '<a href="" id="download" class="button upgradestep" >download</a>';
				$content .= '<a href="" id="unzip" class="button upgradestep" >unzip</a>'; // unzip in autoupgrade/latest
				$content .= '<a href="" id="removeSamples" class="button upgradestep" >removeSamples</a>'; // remove samples (iWheel images)
				$content .= '<a href="" id="backupFiles" class="button upgradestep" >backupFiles</a>'; // backup files
				$content .= '<a href="" id="backupDb" class="button upgradestep" >backupDb</a>';
				$content .= '<a href="" id="upgradeFiles" class="button upgradestep" >upgradeFiles</a>';
				$content .= '<a href="" id="upgradeDb" class="button upgradestep" >upgradeDb</a>';
				$content .= '</div>';

				if (defined('_PS_ALLOW_UPGRADE_UNSTABLE_') AND _PS_ALLOW_UPGRADE_UNSTABLE_ )
				{
					$content .= '<h4>Development tools </h4><div>';
					$content .= '<a href="" name="action" id="svnCheckout"	class="button upgradestep" type="submit" >svnCheckout</a>';
					$content .= '<a href="" name="action" id="svnUpdate"	class="button upgradestep" type="submit" >svnUpdate</a>';
					$content .= '<a href="" name="action" id="svnExport"	class="button upgradestep" type="submit" >svnExport</a>';
					$content .= '<br class="clear"/>';
					$content .= '</div>';
				}
			}

			$content .='	<div id="quickInfo" class="processing" style="height:100px;">&nbsp;</div>';
			// for upgradeDb
			$content .= '<p id="dbResultCheck"></p>';
			$content .= '<p id="dbCreateResultCheck"></p>';
		}
		else
			$content .= '<p>'.$this->l('Your current configuration does not allow upgrade.').'</p>';

		$content .= '<br/><br/><small>'.sprintf($this->l('last datetime check : %s '),date('Y-m-d H:i:s',Configuration::get('PS_LAST_VERSION_CHECK'))).'</span> 
		<a class="button" href="index.php?tab=AdminSelfUpgrade&token='.Tools::getAdminToken('AdminSelfUpgrade'.(int)(Tab::getIdFromClassName(get_class($this))).(int)$cookie->id_employee).'&refreshCurrentVersion=1">'.$this->l('Please click to refresh').'</a>
		</small>';

		$content .= '</fieldset>';
/*		$content .= '<fieldset class="right">
		<legend>Error</legend>
		<div id="errorWindow" > no error yet</div>
		</fieldset>';
		*
		*/
		// information to keep will be in #infoStep
		// temporary infoUpdate will be in #tmpInformation
		$content .= '<script type="text/javascript">';
		// _PS_MODE_DEV_ is available in js
		if (defined('_PS_MODE_DEV_') AND _PS_MODE_DEV_)
			$content .= 'var _PS_MODE_DEV_ = true;';
		$content .= $this->_getJsErrorMsgs();

		$content .= '</script>';
		echo $content;
	}

	public function display()
	{
		/* PrestaShop demo mode */
		if (defined('_PS_MODE_DEMO_') && _PS_MODE_DEMO_)
		{
			echo '<div class="error">'.Tools::displayError('This functionnality has been disabled.').'</div>';
			return;
		}

		if (!file_exists($this->autoupgradePath.DIRECTORY_SEPARATOR.'ajax-upgradetab.php'))
		{
			echo '<div class="error">'.'<img src="../img/admin/warning.gif" /> [TECHNICAL ERROR] '.$this->l('ajax-upgrade.php is missing. please reinstall or reset the module').'</div>';
			return false;
		}
		/* PrestaShop demo mode*/

		
		if(isset($_GET['refreshCurrentVersion']))
		{
			$upgrader = new Upgrader();
			$upgrader->checkPSVersion(true);
			$this->upgrader = $upgrader;
		}
		echo '<style>
.autoupgradeSteps div {  line-height: 30px; }
.upgradestep { margin-right: 5px;padding-left: 10px; padding-right: 5px;}
#upgradeNow.stepok, .autoupgradeSteps a.stepok { background-image: url("../img/admin/enabled.gif");background-position: left center;background-repeat: no-repeat;padding-left: 15px;}
#upgradeNow {-moz-border-bottom-colors: none;-moz-border-image: none;-moz-border-left-colors: none;-moz-border-right-colors: none;-moz-border-top-colors: none;border-color: #FFF6D3 #DFD5AF #DFD5AF #FFF6D3;border-right: 1px solid #DFD5AF;border-style: solid;border-width: 1px;color: #268CCD;font-size: medium;padding: 5px;}
.button-autoupgrade {-moz-border-bottom-colors: none;-moz-border-image: none;-moz-border-left-colors: none;-moz-border-right-colors: none;-moz-border-top-colors: none;border-color: #FFF6D3 #DFD5AF #DFD5AF #FFF6D3;border-right: 1px solid #DFD5AF;border-style: solid;border-width: 1px;color: #268CCD;font-size: medium;padding: 5px;}
.processing {border:2px outset grey;margin-top:1px;overflow: auto;}
#dbResultCheck{ padding-left:20px;}
#checkPrestaShopFilesVersion{margin-bottom:20px;}
#changedList ul{list-style-type:circle}
.changedFileList {margin-left:20px; padding-left:5px;}
.changedNotice li{color:grey;}
.changedImportant li{color:red;font-weight:bold}
</style>';
		$this->displayWarning($this->l('This function is experimental. It\'s highly recommended to make a backup of your files and database before starting the upgrade process.'));

		global $currentIndex;
		// update['name'] = version name
		// update['num'] = only the version
		// update['link'] = download link
		// @TODO

			$this->createCustomToken();
			if ($this->useSvn)
				echo '<div class="error"><h1>'.$this->l('Unstable upgrade').'</h1>
				<p class="warning">'.$this->l('Your current configuration indicate you want to upgrade your system from the unstable development branch, with no version number. If you upgrade, you will not be able to follow the official release process anymore').'.</p>
				</div>';
			$this->_displayUpgraderForm();

			echo '<br/>';
			$this->_displayRollbackForm();

			echo '<br/>';
			$this->_displayForm('autoUpgradeOptions',$this->_fieldsAutoUpgrade,'<a href="" name="options" id="options">'.$this->l('Options').'</a>', '','prefs');
			// @todo manual upload with a form

			// We need jquery 1.6 for json 
			echo '<script type="text/javascript">
				jq13 = jQuery.noConflict(true);
				</script>
				<script type="text/javascript" src="'.__PS_BASE_URI__.'modules/autoupgrade/jquery-1.6.2.min.js"></script>';
			echo '<script type="text/javascript" src="'.__PS_BASE_URI__.'modules/autoupgrade/jquery.xml2json.js"></script>';
			echo '<script type="text/javascript">'.$this->_getJsInit().'</script>';

	}

	private function _getJsInit()
	{
		global $currentIndex;

		if (method_exists('Tools','getAdminTokenLite'))
			$token_preferences = Tools::getAdminTokenLite('AdminPreferences');
		else
			$token_preferences = Tools14::getAdminTokenLite('AdminPreferences');


		$js = '';
		$js .= '
function ucFirst(str) {
	if (str.length > 0) {
		return str[0].toUpperCase() + str.substring(1);
	}
	else {
		return str;
	}
}

function cleanInfo(){
	$("#infoStep").html("reset<br/>");
}

function updateInfoStep(msg){
	if (msg)
	{
		$("#infoStep").html(msg);
		$("#infoStep").attr({ scrollTop: $("#infoStep").attr("scrollHeight") });
	}
}

function addError(msg){
	if (msg)
		$("#errorWindow").html(msg);
}

function addQuickInfo(arrQuickInfo){
	if (arrQuickInfo)
	{
		$("#quickInfo").show();
		for(i=0;i<arrQuickInfo.length;i++)
			$("#quickInfo").append(arrQuickInfo[i]+"<br/>");
		// Note : jquery 1.6 make uses of prop() instead of attr()
		$("#quickInfo").prop({ scrollTop: $("#quickInfo").prop("scrollHeight") },1);
	}
}';

		if ($this->manualMode)
			$js .= 'var manualMode = true;';
		else
			$js .= 'var manualMode = false;';

		$js .= '
var firstTimeParams = '.$this->buildAjaxResult().';
firstTimeParams = firstTimeParams.nextParams;
firstTimeParams.firstTime = "1";

$(document).ready(function(){
	$(".upgradestep").click(function(e)
	{
		e.preventDefault();
		// $.scrollTo("#options")
	});

	// more convenient to have that param for handling success and error
	var requestParams;

		// set timeout to 5 minutes (download can be long)?
		$.ajaxSetup({timeout:300000});


	// prepare available button here, without params ?
	prepareNextButton("#upgradeNow",firstTimeParams);
	prepareNextButton("#rollback",firstTimeParams);
	prepareNextButton("#restoreDb",firstTimeParams);
	prepareNextButton("#restoreFiles",firstTimeParams);

});

/**
 * parseXMLResult is used to handle the return value of the doUpgrade method
 * @xmlRet xml return value
 * @var previousParams contains the precedent post values (to conserve post datas during upgrade db process)
 */

function checkConfig(res)
{
	testRequiredList = $(res.testList[0].test);
	configIsOk = true;

	testRequiredList.each(function()
	{
		result = $(this).attr("result");
		if (result == "fail") configIsOk = false;
	});

	if (!configIsOk)
	{
		alert("Configuration install problem");
		return "fail";
	}
	else
		return "ok";
}

function handleXMLResult(xmlRet, previousParams)
{
	// use xml2json and put the result in the global var
	// this will be used in after** javascript functions
	resGlobal = $.xml2json(xmlRet);
	result = "ok";
	switch(previousParams.upgradeDbStep)
	{
		case 0: // getVersionFromDb
		resGlobal.result = "ok";
		break;
		case 1: // getVersionFromDb
		result = resGlobal.result;
		break;
		case 2: // checkConfig
		result = checkConfig(resGlobal);
		break;
		case 3: // doUpgrade:
		result = resGlobal.result;
		break;
		case 4: // upgradeComplete
		result = resGlobal.result;
		break;
	}

	if (result == "ok")
	{
		nextParams = previousParams;
		nextParams.upgradeDbStep = parseInt(previousParams.upgradeDbStep)+1;
		if(nextParams.upgradeDbStep >= 4)
		{
			resGlobal.next = "upgradeComplete";
			nextParams.typeResult = "json";
		}
		else
			resGlobal.next = "upgradeDb";
		resGlobal = {next:resGlobal.next,nextParams:nextParams,status:"ok"};

	}
	else
	{
		$("#dbResultCheck")
			.addClass("fail")
			.removeClass("ok")
			.show("slow");
		$("#dbCreateResultCheck")
			.hide("slow");

		// propose rollback if there is an error
		if (confirm("An error happen\r\n'.$this->l('You may need to rollback.').'"))
			resGlobal = {next:"rollback",nextParams:{typeResult:"json"},status:"error"};
	}

	return resGlobal;
};

var resGlobal = {};
function afterUpgradeNow()
{
	$("#upgradeNow").unbind();
	$("#upgradeNow").replaceWith("<span class=\"button-autoupgrade\">'.$this->l('Upgrading PrestaShop').'</span>");
}

function afterUpgradeComplete()
{
	$("#pleaseWait").hide();
	$("#dbResultCheck")
		.addClass("ok")
		.removeClass("fail")
		.html("<p>'.$this->l('upgrade complete. Please check your front-office theme is functionnal (try to make an order, check theme)').'</p>")
		.show("slow")
		.append("<a href=\"index.php?tab=AdminPreferences&token='.$token_preferences.'\" class=\"button\">'.$this->l('activate your shop here').'</a>");
	$("#dbCreateResultCheck")
		.hide("slow");
	$("#infoStep").html("<h3>'.$this->l('Upgrade Complete ! ').'</h3>");
}

/**
 * afterBackupDb display the button
 *
 */
function afterBackupDb()
{
	$("#restoreDbContainer").html("<a href=\"\" class=\"upgradestep button\" id=\"restoreDb\">restoreDb</a> '.$this->l('click to restore database').'");
	prepareNextButton("#restoreDb",{});
}

function afterRestoreDb()
{
	$("#restoreDbContainer").html("");
}

function afterRestoreFiles()
{
	$("#restoreFilesContainer").html("");
}

function afterBackupFiles()
{
	$("#restoreFilesContainer").html("<div id=\"restoreFilesContainer\"><a href=\"\" class=\"upgradestep button\" id=\"restoreFiles\">restoreFiles</a> '.$this->l('click to restore files').'");
	prepareNextButton("#restoreFiles",{});

}

function doAjaxRequest(action, nextParams){
		$("#pleaseWait").show();
		// myNext, used when json is not available but response is correct
		myNext = nextParams;
		req = $.ajax({
			type:"POST",
			url : "'. __PS_BASE_URI__ .trim($this->adminDir,DIRECTORY_SEPARATOR).'/autoupgrade/ajax-upgradetab.php'.'",
			async: true,
			data : {
				dir:"'.trim($this->adminDir,DIRECTORY_SEPARATOR).'",
				ajaxMode : "1",
				token : "'.$this->token.'",
				tab : "'.get_class($this).'",
				action : action,
				params : nextParams
			},
			success : function(res,textStatus,jqXHR)
			{
				$("#pleaseWait").hide();
				if(eval("typeof nextParams") == "undefined")
				{
					nextParams = {typeResult : "json"};
				}

				if (nextParams.typeResult == "xml")
				{
					res = handleXMLResult(res,nextParams);
				}
				else
				{
					try{
						res = $.parseJSON(res);
						nextParams = res.nextParams;
					}
					catch(e){
						res = {status : "error"};
						alert("error during "+action);
						/*
						nextParams = {
							error:"0",
							next:"cancelUpgrade",
							nextDesc:"Error detected during ["+action+"] step, reverting...",
							nextQuickInfo:[],
							status:"ok",
							"stepDone":true
						}
						*/
					}
				}

				if (res.status == "ok")
				{
					$("#"+action).addClass("done");
					if (res.stepDone)
						$("#"+action).addClass("stepok");

					// if a function "after[action name]" exists, it should be called.
					// This is used for enabling restore buttons for example
					funcName = "after"+ucFirst(action);
					if (typeof funcName == "string" &&
						eval("typeof " + funcName) == "function") {
						eval(funcName+"()");
					}

					handleSuccess(res,nextParams.typeResult);
				}
				else
				{
					// display progression
					$("#"+action).addClass("done");
					$("#"+action).addClass("steperror");
					handleError(res);
				}
			},
			error: function(res,textStatus,jqXHR)
			{
				$("#pleaseWait").hide();
				if (textStatus == "timeout" && action == "download")
				{
					updateInfoStep("'.$this->l('Your server can\'t download the file. Please upload it first by ftp in your admin/autoupgrade directory').'");
				}
				else
				{
					updateInfoStep("[Server Error] Status message : " + textStatus);
				}
			}
		});
	};

/**
 * prepareNextButton make the button button_selector available, and update the nextParams values
 *
 * @param button_selector $button_selector
 * @param nextParams $nextParams
 * @return void
 */
function prepareNextButton(button_selector, nextParams)
{
	$(button_selector).unbind();
	$(button_selector).click(function(e){
		e.preventDefault();
		$("#currentlyProcessing").show();
';
		if (defined('_PS_MODE_DEV_') AND _PS_MODE_DEV_)
			$js .= 'addQuickInfo(["[DEV] request : "+$(this).attr("id")]);';
		$js .= '
	action = button_selector.substr(1);
	res = doAjaxRequest(action, nextParams);
	});
}

/**
 * handleSuccess
 * res = {error:, next:, nextDesc:, nextParams:, nextQuickInfo:,status:"ok"}
 * @param res $res
 * @return void
 */
function handleSuccess(res)
{
	updateInfoStep(res.nextDesc);
	if (res.next != "")
	{
		addQuickInfo(res.nextQuickInfo);

		$("#"+res.next).addClass("nextStep");
		if (manualMode)
		{
			prepareNextButton("#"+res.next,res.nextParams);
			alert("manually go to "+res.next+" button ");
		}
		else
		{
			// @TODO :
			// 1) instead of click(), call a function.
			doAjaxRequest(res.next,res.nextParams);
			// 2) remove all step link (or show them only in dev mode)
			// 3) when steps link displayed, they should change color when passed
		}
	}
	else
	{
		// Way To Go, end of upgrade process
		addQuickInfo(["End of upgrade process"]);
	}
}

// res = {nextParams, NextDesc}
function handleError(res)
{
	// display error message in the main process thing
	updateInfoStep(res.nextDesc);
	addQuickInfo(res.nextQuickInfo);
	// In case the rollback button has been deactivated, just re-enable it
	prepareNextButton("#rollback",res.nextParams);
	// ask if you want to rollback
	// @TODO !!!
	if (confirm(res.NextDesc+"\r\r'.$this->l('Do you want to rollback ?').'"))
	{
		if (manualMode)
			alert("'.$this->l('Please go manually go to rollback button').'");
		else
		{
			$("#rollback").click();
		}

	}
}
';
// ajax to check md5 files
		$js .= 'function addModifiedFileList(title, fileList, css_class)
{
	subList = $("<ul class=\"changedFileList "+css_class+"\"></ul>");

	$(fileList).each(function(k,v){
		$(subList).append("<li>"+v+"</li>");
	});
	$("#changedList").append("<h3><a class=\"toggleSublist\">"+title+"</a> (" + fileList.length + ")</h3>");
	$("#changedList").append(subList);
	$("#cchangedList").append("<br/>");

}';
	if(!file_exists($this->autoupgradePath.DIRECTORY_SEPARATOR.'ajax-upgradetab.php'))
		$js .= '$(document).ready(function(){
			$("#checkPrestaShopFilesVersion").html("<img src=\"../img/admin/warning.gif\" /> [TECHNICAL ERROR] ajax-upgradetab.php '.$this->l('is missing. please reinstall the module').'");
			})';
	else
		$js .= '$(document).ready(function(){
	$.ajax({
			type:"POST",
			url : "'. __PS_BASE_URI__ . trim($this->adminDir,DIRECTORY_SEPARATOR).'/autoupgrade/ajax-upgradetab.php",
			async: true,
			data : {
				dir:"'.trim($this->adminDir,DIRECTORY_SEPARATOR).'",
				token : "'.$this->token.'",
				tab : "'.get_class($this).'",
				action : "checkFilesVersion",
				params : {}
			},
			success : function(res,textStatus,jqXHR)
			{
					res = $.parseJSON(res);
					answer = res.nextParams;
					$("#checkPrestaShopFilesVersion").html("<span> "+answer.msg+" </span> ");
					if (answer.status == "error")
						$("#checkPrestaShopFilesVersion").prepend("<img src=\"../img/admin/warning.gif\" /> ");
					else
					{
						$("#checkPrestaShopFilesVersion").prepend("<img src=\"../img/admin/warning.gif\" /> ");
						$("#checkPrestaShopFilesVersion").append("<a id=\"toggleChangedList\" class=\"button\" href=\"\">'.$this->l('See or hide the list').'</a><br/>");
						$("#checkPrestaShopFilesVersion").append("<div id=\"changedList\" style=\"display:none \"><br/>");
						if(answer.result.core.length)
							addModifiedFileList("'.$this->l('Core file(s)').'", answer.result.core, "changedImportant");
						if(answer.result.mail.length)
							addModifiedFileList("'.$this->l('Mail file(s)').'", answer.result.mail, "changedNotice");
						if(answer.result.translation.length)
							addModifiedFileList("'.$this->l('Translation file(s)').'", answer.result.translation, "changedNotice");

						$("#toggleChangedList").bind("click",function(e){e.preventDefault();$("#changedList").toggle();});
						$(".toggleSublist").live("click",function(e){e.preventDefault();$(this).parent().next().toggle();});
				}
			}
			,
			error: function(res, textStatus, jqXHR)
			{
				if (textStatus == "timeout" && action == "download")
				{
					updateInfoStep("'.$this->l('Your server can\'t download the file. Please upload it first by ftp in your admin/autoupgrade directory').'");
				}
				else
				{
					// technical error : no translation needed
					$("#checkPrestaShopFilesVersion").html("<img src=\"../img/admin/warning.gif\" /> [TECHNICAL ERROR] Unable to check md5 files");
				}
			}
		})
});';
		return $js;
	}


	/**
	 * @desc extract a zip file to the given directory
	 * @return bool success
	 * we need a copy of it to be able to restore without keeping Tools and Autoload stuff
	 */
	private static function ZipExtract($fromFile, $toDir)
	{
		if (!file_exists($toDir))
			if (!@mkdir($toDir,0777))
			{
				$this->next = 'error';
				$this->nextDesc = sprintf($this->l('unable to create directory %s'),$toDir);
				return false;
			}

		if (!self::$force_pclZip && class_exists('ZipArchive', false))
		{
			$zip = new ZipArchive();
			if ($zip->open($fromFile) === true)
			{
				if (@$zip->extractTo($toDir.'/') 
					&& $zip->close()
				)
				{
					return true;
				}
				else
				{
					return false;
				}
				return false;
			}
			else
				return false;
		}
		else
		{
			// todo : no relative path
			if (!class_exists('PclZip',false))
				require_once(dirname(__FILE__).'/pclzip.lib.php');

			$zip = new PclZip($fromFile);
			$list = $zip->extract(PCLZIP_OPT_PATH, $toDir);
			foreach ($list as $extractedFile)
				if ($extractedFile['status'] != 'ok')
					return false;

			return true;
		}
	}

	private function _listArchivedFiles()
	{
		if (!empty($this->currentParams['backupFilesFilename']))
		{
			if (!self::$force_pclZip && class_exists('ZipArchive', false))
			{
				$files=array();
				if ($zip = zip_open($this->currentParams['backupFilesFilename']))
				{
					while ($entry=zip_read($zip))
						$files[] = zip_entry_name($entry);
					zip_close($zip);
					return $files;
				}
				// @todo : else throw new Exception()
			}
			else
			{
				require_once(dirname(__FILE__).'/pclzip.lib.php');
				if ($zip = new PclZip($this->currentParams['backupFilesFilename']))
					return $zip->listContent();
				// @todo : else throw new Exception()
			}
		}
		return false;
	}

	/**
	 *	bool _skipFile : check whether a file is in backup or restore skip list
	 *
	 * @param type $file : current file or directory name eg:'.svn' , 'settings.inc.php'
	 * @param type $fullpath : current file or directory fullpath eg:'/home/web/www/prestashop/img'
	 * @param type $way : 'backup' , 'upgrade'
	 */
	private function _skipFile($file,$fullpath,$way='backup')
	{
		$fullpath = str_replace('\\','/', $fullpath); // wamp compliant
		$rootpath = str_replace('\\','/', $this->prodRootDir);
		switch ($way)
		{
			case 'backup':
				if (in_array($file, $this->backupIgnoreFiles))
					return true;

				foreach($this->backupIgnoreAbsoluteFiles as $path)
					if ($file == 'img')
						if (strpos($fullpath, $rootpath.$path) !== false)
							return true;
				break;

			case 'upgrade':
				if (in_array($file, $this->excludeFilesFromUpgrade))
					return true;

				foreach ($this->excludeAbsoluteFilesFromUpgrade as $path)
					if (strpos($fullpath, $rootpath.$path) !== false)
						return true;
				break;
			// default : if it's not a backup or an upgrade, juste skip the file
			default:
				return false;
		}
	}
}


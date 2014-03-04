<?
global $MESS;
$PathInstall = str_replace('\\', '/', __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall) - strlen('/index.php'));
IncludeModuleLangFile($PathInstall . '/install.php');
include($PathInstall . '/version.php');

if (class_exists('digital_delivery'))
	return;

class digital_delivery extends CModule {

	var $MODULE_ID = "digital.delivery";//compatibility
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $PARTNER_NAME;
	public $PARTNER_URI;
	public $MODULE_GROUP_RIGHTS = 'N';
	public $NEED_MAIN_VERSION = '';
	public $NEED_MODULES = array('sale', 'catalog');

	public function __construct() {
		$arModuleVersion = array();

		$path = str_replace('\\', '/', __FILE__);
		$path = substr($path, 0, strlen($path) - strlen('/index.php'));
		include($path . '/version.php');

		if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}

		$this->PARTNER_NAME = GetMessage('DIGITAL_DELIVERY_PARTNER_NAME');
		$this->PARTNER_URI = 'http://ddelivery.ru/';

		$this->MODULE_NAME = GetMessage('DIGITAL_DELIVERY_MODULE_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('DIGITAL_DELIVERY_MODULE_DESCRIPTION');
	}

	public function DoInstall() {
		if ($GLOBALS['APPLICATION']->GetGroupRight('main') < 'W')
			return;

		if (is_array($this->NEED_MODULES) && !empty($this->NEED_MODULES))
			foreach ($this->NEED_MODULES as $module)
				if (!IsModuleInstalled($module))
					$this->ShowForm('ERROR', GetMessage('DIGITAL_DELIVERY_NEED_MODULES', array('#MODULE#' => $module)));

		if (strlen($this->NEED_MAIN_VERSION) <= 0 || version_compare(SM_VERSION, $this->NEED_MAIN_VERSION) >= 0) {
			RegisterModuleDependences('sale', 'onSaleDeliveryHandlersBuildList', $this->MODULE_ID, 'CDigitalDelivery', 'Init', 100);
            RegisterModuleDependences('sale', 'OnOrderNewSendEmail', $this->MODULE_ID, 'CDigitalDelivery', 'OnOrderNewSendEmail');
			RegisterModule($this->MODULE_ID);
			$this->ShowForm('OK', GetMessage('MOD_INST_OK'));
		}
		else
			$this->ShowForm('ERROR', GetMessage('DIGITAL_DELIVERY_NEED_RIGHT_VER', array('#NEED#' => $this->NEED_MAIN_VERSION)));
	}


    function InstallFiles()
    {
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/digital.delivery/install/ddelivery.js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/digital.delivery/ddelivery.js", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/digital.delivery/install/ddelivery.php", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/ddelivery.php", true, true);

        return true;
    }

	public function DoUninstall() {
		if ($GLOBALS['APPLICATION']->GetGroupRight('main') < 'W')
			return;

		if ($_REQUEST['step'] < 2)
			$this->ShowDataSaveForm();
		elseif ($_REQUEST['step'] == 2) {
			UnRegisterModuleDependences('sale', 'onSaleDeliveryHandlersBuildList', $this->MODULE_ID, 'CDigitalDelivery', 'Init');
			UnRegisterModuleDependences('sale', 'OnOrderNewSendEmail', $this->MODULE_ID, 'CDigitalDelivery', 'OnOrderNewSendEmail');
			//UnRegisterModuleDependences('sale', 'OnOrderUpdate', $this->MODULE_ID, 'CDigitalDelivery', 'OnOrderUpdate');
			UnRegisterModule($this->MODULE_ID);
			$this->ShowForm('OK', GetMessage('MOD_UNINST_OK'));
		}
	}

	private function ShowForm($type, $message, $buttonName = '') {
		$keys = array_keys($GLOBALS);
		for ($i = 0; $i < count($keys); $i++)
			if ($keys[$i] != 'i' && $keys[$i] != 'GLOBALS' && $keys[$i] != 'strTitle' && $keys[$i] != 'filepath')
				global ${$keys[$i]};

		$PathInstall = str_replace('\\', '/', __FILE__);
		$PathInstall = substr($PathInstall, 0, strlen($PathInstall) - strlen('/index.php'));
		IncludeModuleLangFile($PathInstall . '/install.php');

		$APPLICATION->SetTitle(GetMessage('DIGITAL_DELIVERY_MODULE_NAME'));
		include($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');
		echo CAdminMessage::ShowMessage(array('MESSAGE' => $message, 'TYPE' => $type));
		?>
		<form action='<?= $APPLICATION->GetCurPage() ?>' method='get'>
			<p>
				<input type='hidden' name='lang' value='<?= LANG ?>' />
				<input type='submit' value='<?= strlen($buttonName) ? $buttonName : GetMessage('MOD_BACK') ?>' />
			</p>
		</form>
		<?
		include($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
		die();
	}

	private function ShowDataSaveForm() {
		$keys = array_keys($GLOBALS);
		for ($i = 0; $i < count($keys); $i++)
			if ($keys[$i] != 'i' && $keys[$i] != 'GLOBALS' && $keys[$i] != 'strTitle' && $keys[$i] != 'filepath')
				global ${$keys[$i]};

		$PathInstall = str_replace('\\', '/', __FILE__);
		$PathInstall = substr($PathInstall, 0, strlen($PathInstall) - strlen('/index.php'));
		IncludeModuleLangFile($PathInstall . '/install.php');

		$APPLICATION->SetTitle(GetMessage('DIGITAL_DELIVERY_MODULE_NAME'));
		include($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');
		?>
		<form action='<?= $APPLICATION->GetCurPage() ?>' method='get'>
			<?= bitrix_sessid_post() ?>
			<input type='hidden' name='lang' value='<?= LANG ?>' />
			<input type='hidden' name='id' value='<?= $this->MODULE_ID ?>' />
			<input type='hidden' name='uninstall' value='Y' />
			<input type='hidden' name='step' value='2' />
			<? CAdminMessage::ShowMessage(GetMessage('MOD_UNINST_WARN')) ?>
			<? /* <p><?echo GetMessage('MOD_UNINST_SAVE')?></p>
			  <p>
			  <input type='checkbox' name='savedata' id='savedata' value='Y' checked='checked' /><label for='savedata'><?echo GetMessage('MOD_UNINST_SAVE_TABLES')?></label><br />
			  </p> */ ?>
			<input type='submit' name='inst' value='<? echo GetMessage('MOD_UNINST_DEL') ?>' />
		</form>
		<?
		include($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
		die();
	}

}
?>
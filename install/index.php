<?

use \Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Config\Option;

Loc::LoadMessages(__FILE__);

class polus_pricehandler extends \CModule
{
    var $MODULE_ID = "polus.pricehandler";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;

    function __construct()
    {
        $arModuleVersion = array();

        include(_DIR_ . "/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->VODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->MODULE_NAME = Loc::getMessage("POLUS_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("POLUS_MODULE_DESCRIPTION");
        $this->PARTNER_NAME = Loc::getMessage("PARTNER_NAME");

    }

    public function DoInstall()
    {
        global $APPLICATION;

         if(CheckVersion(\Bitrix\Main\ModuleManager::getVersion("main"), "14.00.00"))
         {
             \Bitrix\Main\ModuleManager::RegisterModule($this->MODULE_ID);

             $this->InstallDB();
             $this->InstallEvents();

             $this->InstallFiles();

             $APPLICATION->IncludeAdminFile(Loc::getMessage("MODULE_INSTALL_TITLE"), __DIR__ . '/step.php');
         }
         else
         {
             $APPLICATION->ThrowException(Loc::getMessage("INSTALL_ERROR"));
         }

    }

    public function DoUninstall()
    {
        global $APPLICATION;

        $this->UnInstallDB();
        $this->UnInstallEvents();

        $this->UnInstallFiles();

        \Bitrix\Main\ModuleManager::UnRegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile(Loc::getMessage("MODULE_UNINSTALL_TITLE"), __DIR__ . '/unstep.php');
    }

    public function InstallFiles()
    {
        //CopyDirFiles(__DIR__ . '/components', $_SERVER["DOCUMENT_ROOT"] . '/bitrix/components/', true, true);

        return true;
    }

    public function UnInstallFiles()
    {
        //DeleteDirFilesEx('/bitrix/components/citrus/track.changes');

        return true;
    }

    public function InstallDB()
    {
        //$this->createHighloadBlock();

        return true;
    }

    public function UnInstallDB()
    {
        //$this->deleteHighloadBlock();

        return true;
    }

    public function InstallEvents()
    {
        $eventManager = \Bitrix\Main\EventManager::getInstance();

        $eventManager->registerEventHandler(
            'catalog',
            'OnGetOptimalPrice',
            'polus.pricehandler',
            '\Polus\Sale\Handler', 'onGetOptimalPriceHandler'
        );

        return true;
    }

    public function UnInstallEvents()
    {
        $eventManager = \Bitrix\Main\EventManager::getInstance();

        $eventManager->unRegisterEventHandler(
            'catalog',
            'OnGetOptimalPrice',
            'polus.pricehandler',
            '\Polus\Sale\Handler', 'onGetOptimalPriceHandler'
        );

        return true;
    }
}
?>


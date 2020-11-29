<?php

use Bitrix\Main\Loader;

IncludeModuleLangFile(__FILE__);

Loader::registerAutoLoadClasses('polus.pricehandler',
    array(
        "Polus\\Tools\\CModuleOptions" => 'lib/Tools/CModuleOptions.php',
        "Polus\\Tools\\Options" => 'lib/Tools/Options.php',
        "Polus\\Constants" => 'lib/Constants.php',
        "Polus\\Sale\\Handler" => 'lib/Sale/Handler.php',
        "Polus\\Sale\\Price" => 'lib/Sale/Price.php'
    )
);

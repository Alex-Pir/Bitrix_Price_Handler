<?php
namespace Polus\Tools;

use Polus\Constants;
use COption;

/**
 * Класс для удобного получения настроек модуля
 *
 * Class Options
 * @package Polus\Tools
 */
class Options
{
    /**
     * Получение значения пороговой цены, при котором происходит переключение на другой тип цен
     *
     * @return mixed
     */
    public static function getPriceChangeValue()
    {
        return COption::GetOptionString(Constants::MODULE_ID, Constants::PARAM_PRICE_CHANGE, '');
    }

    /**
     * Получение состояния чекбокса активности модуля
     * @return bool
     */
    public static function isModuleActive()
    {
        $check = COption::GetOptionString(Constants::MODULE_ID, Constants::PARAM_USE_MODULE, '');

        if ($check === 'Y')
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Получение ID типа цен, на который произойдет изменение
     *
     * @return mixed
     */
    public static function getPriceTypeChange()
    {
        return COption::GetOptionString(Constants::MODULE_ID, Constants::PARAM_PRICE_TYPE_TWO, '');
    }
}
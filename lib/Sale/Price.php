<?php

namespace Polus\Sale;

use Polus\Constants;
use CPrice;
use CSaleBasket;
use COption;
use CCatalogDiscount;

/**
 * Класс для работы с ценами
 *
 * Class Price
 * @package Polus\Sale
 */
class Price
{
    /**
     * Получение базоваой цены с учетом стандартных скидок
     *
     * @param $productID
     * @return mixed
     */
    public static function getBasePrice($productID)
    {
        $arBasePrice = CPrice::GetBasePrice($productID);

        $arResult["PRICE"] = $arBasePrice["PRICE"];
        $arResult["CURRENCY"] = $arBasePrice["CURRENCY"];
        $arResult["ID"] = $arBasePrice["ID"];
        $arResult["CATALOG_GROUP_NAME"] = $arBasePrice["CATALOG_GROUP_NAME"];


        $arDiscount = self::getDiscount($productID, $arBasePrice["CATALOG_GROUP_ID"]);

        $arResult["PRICE"] = self::getPriceWithDiscount($arResult["PRICE"], $arDiscount);

        return $arResult;
    }

    /**
     * Получение цены типа $piceType на товар
     *
     * @param $productID
     * @param $priceType
     * @return array
     */
    public static function getSomethingPrice($productID, $priceType)
    {

        $arResult = array();

        $dbRes = CPrice::GetList(
            array(),
            array(
                "PRODUCT_ID" => $productID,
                "CATALOG_GROUP_ID" => $priceType
            )
        );
        if ($arRes = $dbRes->Fetch())
        {
            $arResult["PRICE"] = $arRes["PRICE"];
            $arResult["CURRENCY"] = $arRes["CURRENCY"];

        }

        return $arResult;
    }

    /**
     * Получение суммы корзины в базовом типе цен.
     * Нужно для проверки условия, превысила ли стоимость текущей корзины заданный порог.
     * Это нужно, так как если цена уже была изменена обработчиком, то на следующем шаге
     * вернется стоимость корзину в ТЕКУЩЕМ типе цен.
     *
     * @return float|int
     */
    public static function getBasketPrice()
    {
        $basketPrice = 0;

        $dbBasketItems = CSaleBasket::GetList(false,
            array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => "NULL"
            ),
            false,
            false,
            array("ID", "MODULE", "PRODUCT_ID", "CALLBACK_FUNC", "QUANTITY", "DELAY", "CAN_BUY", "PRICE")
        );
        while ($arItem = $dbBasketItems->Fetch()) {
            if ($arItem['DELAY'] == 'N' && $arItem['CAN_BUY'] == 'Y') {

                $arProductPrice = self::getBasePrice($arItem["PRODUCT_ID"]);

                $basketPrice += $arProductPrice["PRICE"] * $arItem["QUANTITY"];
            }
        }

        return $basketPrice;
    }

    /**
     * получение стоимости корзины с типом цен, заданном в настройках модуля.
     *
     * @return float|int
     */
    public static function getBasketWithChangePrice()
    {
        $basketPrice = 0;

        $dbBasketItems = CSaleBasket::GetList(false,
            array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => "NULL"
            ),
            false,
            false,
            array("ID", "MODULE", "PRODUCT_ID", "CALLBACK_FUNC", "QUANTITY", "DELAY", "CAN_BUY", "PRICE")
        );
        while ($arItem = $dbBasketItems->Fetch()) {
            if ($arItem['DELAY'] == 'N' && $arItem['CAN_BUY'] == 'Y') {

                $arProductPrice = self::getChangePrice($arItem["PRODUCT_ID"]);

                $basketPrice += $arProductPrice["PRICE"] * $arItem["QUANTITY"];
            }
        }

        return $basketPrice;
    }

    /**
     * Получение цены на товар с тем типом цен, который задан в настройках модуля.
     * Учитываются стандартные скидки корзины
     *
     * @param $productID
     * @return array
     */
    public static function getChangePrice($productID)
    {
        $arResult = array();

        $priceTypeTwo = COption::GetOptionString(Constants::MODULE_ID, Constants::PARAM_PRICE_TYPE_TWO, '');
        $priceChange = COption::GetOptionString(Constants::MODULE_ID, Constants::PARAM_PRICE_CHANGE, '');

        if (!empty($priceTypeTwo) && ((!empty($priceChange) && $priceChange >= 0)))
        {
            $arResult = self::getSomethingPrice($productID, $priceTypeTwo);
        }

        $arDiscount = self::getDiscount($productID, $priceTypeTwo);

        $arResult["PRICE"] = self::getPriceWithDiscount($arResult["PRICE"], $arDiscount);


        return $arResult;
    }

    /**
     * Получение массива скидок на товар с типом цен $priceID
     *
     * @param $productID
     * @param $priceID
     * @return mixed
     */
    public static function getDiscount($productID, $priceID)
    {
        global $USER;

        $arDiscounts = CCatalogDiscount::GetDiscountByProduct(
            $productID,
            $USER->GetUserGroupArray(),
            "N",
            $priceID,
            Constants::SITE_ID
        );

        return $arDiscounts;
    }


    /**
     * Расчет скидок, примененных к данному товару (стандартных)
     *
     * @param $price - цена товара
     * @param $arDiscount - массив скидок для данного товара
     * @return float|int - цена с учетом скидок
     */
    public static function getPriceWithDiscount($price, $arDiscount)
    {
        $result = $price;

        if (!empty($arDiscount))
        {
            foreach ($arDiscount as $discount)
            {
                switch ($discount["VALUE_TYPE"])
                {
                    case "P":
                        $result = $price -  ($price * $discount["VALUE"] / 100);
                        break;
                    default:
                        $result = $price - $discount["VALUE"];
                        break;
                }
            }
        }

        return $result;

    }

    /**
     * Название типа цен, на который происходит изменение
     *
     * @return mixed
     */
    public static function getChangePriceName()
    {
        $priceTypeTwo = COption::GetOptionString(Constants::MODULE_ID, Constants::PARAM_PRICE_TYPE_TWO, '');

        $dbRes = CPrice::GetList(
            array(),
            array(
                "CATALOG_GROUP_ID" => $priceTypeTwo
            ),
            false,
            false,
            array(
                "CATALOG_GROUP_NAME"
            )
        );
        if ($arRes = $dbRes->Fetch())
        {
            $priceTypeName = $arRes["CATALOG_GROUP_NAME"];
        }

        return $priceTypeName;

    }

    /**
     * Название базового типа цен
     *
     * @return mixed
     */
    public static function getBasePriceName()
    {
        $basketPrice = self::getBasketPrice();

        $dbRes = CPrice::GetList(
            array(),
            array(
                "BASE" => "Y"
            ),
            false,
            false,
            array(
                "CATALOG_GROUP_NAME"
            )
        );
        if ($arRes = $dbRes->Fetch())
        {
            $priceTypeName = $arRes["CATALOG_GROUP_NAME"];
        }

        return $priceTypeName;

    }



    /**
     * Получение названия текущего типа цен
     *
     * @return mixed
     */
    public static function getCurrentPriceName()
    {
        $basketPrice = self::getBasketPrice();

        $priceTypeTwo = COption::GetOptionString(Constants::MODULE_ID, Constants::PARAM_PRICE_TYPE_TWO, '');
        $priceChange = COption::GetOptionString(Constants::MODULE_ID, Constants::PARAM_PRICE_CHANGE, '');

        if ($basketPrice >= $priceChange)
        {

            $priceTypeName = self::getChangePriceName();
        }
        else
        {
            $priceTypeName = self::getBasePriceName();
        }

        return $priceTypeName;
    }

}
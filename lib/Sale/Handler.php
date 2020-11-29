<?php

namespace Polus\Sale;

use Polus\Constants;
use CSaleBasket;
use CCatalogProduct;
use COption;
use Protobuf\Exception;
use CPrice;

/**
 * Класс, предназначенный для обработки событий модуля 'sale'
 *
 * Class Handler
 * @package Polus\Sale
 */
class Handler
{

    /**
     * Обработка события получения оптимальной цены в корзине и оформлении заказа (событие - OnGetOptimalPrice)
     *
     * @param $productID
     * @param int $quantity
     * @param array $arUserGroups
     * @param string $renewal
     * @param array $arPrices
     * @param false $siteID
     * @param false $arDiscountCoupons
     * @return array[]
     */
    public static function onGetOptimalPriceHandler($productID, $quantity = 1, $arUserGroups = array(), $renewal = "N", $arPrices = array(), $siteID = false, $arDiscountCoupons = false)
    {
        $LocalPrice = 0;

        $useModule = COption::GetOptionString(Constants::MODULE_ID, Constants::PARAM_USE_MODULE, '');

        $arBasePrice = Price::getBasePrice($productID);

            if ($useModule === 'Y')
            {
                $priceTypeTwo = COption::GetOptionString(Constants::MODULE_ID, Constants::PARAM_PRICE_TYPE_TWO, '');
                $priceChange = COption::GetOptionString(Constants::MODULE_ID, Constants::PARAM_PRICE_CHANGE, '');


                if (!empty($priceTypeTwo) && !empty($priceChange)) {
                    if ($priceChange >= 0) {

                        // Выведем актуальную корзину для текущего пользователя
                        $LocalPrice = Price::getBasketPrice();

                        if ($LocalPrice >= $priceChange) {
                            $arPrice = Price::getSomethingPrice($productID, $priceTypeTwo);


                            if (empty($arPrice["PRICE"])) {
                                $arPrice = $arBasePrice;

                            }

                        } else {
                            $arPrice = $arBasePrice;

                        }

                        return array(
                            'PRICE' => array(
                                "ID" => $productID,
                                'CATALOG_GROUP_ID' => 1,
                                'PRICE' => $arPrice["PRICE"],
                                'CURRENCY' => $arPrice["CURRENCY"],
                                'ELEMENT_IBLOCK_ID' => $productID,
                                'VAT_INCLUDED' => "Y",
                            ),
                            'DISCOUNT' => array(
                                'VALUE' => $discount,
                                'CURRENCY' => "RUB",
                            ),
                        );
                    }
                }

            }
            else
            {
                $arPrice = $arBasePrice;

            }

            return array(
                'PRICE' => array(
                    "ID" => $productID,
                    'CATALOG_GROUP_ID' => 1,
                    'PRICE' => $arPrice["PRICE"],
                    'CURRENCY' => $arPrice["CURRENCY"],
                    'ELEMENT_IBLOCK_ID' => $productID,
                    'VAT_INCLUDED' => "Y",
                ),
                'DISCOUNT' => array(
                    'VALUE' => $discount,
                    'CURRENCY' => "RUB",
                ),
            );


    }
}
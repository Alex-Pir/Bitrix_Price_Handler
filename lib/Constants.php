<?php

namespace Polus;

class Constants
{
    /**
     * ID данного модуля
     */
    const MODULE_ID = "polus.pricehandler";

    /**
     * Параметр 'Включить смену типов цен'
     */
    const PARAM_USE_MODULE = "use_module";

    /**
     * Параметр 'Тип цен, на который произойдет изменение'
     */
    const PARAM_PRICE_TYPE_TWO = "price_type_two";

    /**
     * Параметр 'Сумма, при которой произойдет смена типов цен'
     */
    const PARAM_PRICE_CHANGE = "price_change";

    /**
     * ID сайта. Если подставлять стандартную константу, в админке она заменяется на id языка
     */
    const SITE_ID = "s1";

    /**
     * Константы для замены одного типа цен на другой (так нужно было по условтию задачи).
     * Этот вариант проще и дешевле в плане ресурсов, чем каждый раз обращаться к базе за параметрами,
     * если бы это было в настройках модуля
     */
    const PRICE_CHANGE_NAME_MISTAKE = "Цены ИМ";
    const PRICE_CHANGE_NAME_RIGT = "Интернет";
}
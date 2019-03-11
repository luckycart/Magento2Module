<?php
/**
 * Lucky Cart implementation on Magento 2
 * Copyright (C) 2019  Luckycart
 * 
 * This file is part of Yuukoo/Luckycart.
 * 
 * Yuukoo/Luckycart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Yuukoo\Luckycart\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{

	const XML_PATH_LUCKYCART_GENERAL_ENABLE			= 'luckycart/settings/enable';
	const XML_PATH_LUCKYCART_API_KEY				= 'luckycart/settings/api_key';
	const XML_PATH_LUCKYCART_API_SECRET				= 'luckycart/settings/api_secret';
	const XML_PATH_LUCKYCART_SELECT_BRAND			= 'luckycart/select/brand';
	const XML_PATH_LUCKYCART_FIELDS_INVOICE_COUNTRY	= 'luckycart/fields/invoice_country';
	const XML_PATH_LUCKYCART_FIELDS_OPTIN			= 'luckycart/fields/optin';
	const XML_PATH_LUCKYCART_FIELDS_EMAIL			= 'luckycart/fields/email';
	const XML_PATH_LUCKYCART_FIELDS_PAYMENT_METHOD	= 'luckycart/fields/payment_method';
	const XML_PATH_LUCKYCART_FIELDS_FIRSTNAME		= 'luckycart/fields/firstname';
	const XML_PATH_LUCKYCART_FIELDS_LASTNAME		= 'luckycart/fields/lastname';
	const XML_PATH_LUCKYCART_FIELDS_DISCOUNT_CODE	= 'luckycart/fields/discount_code';
	const XML_PATH_LUCKYCART_FIELDS_LANG			= 'luckycart/fields/lang';
	const XML_PATH_LUCKYCART_FIELDS_STORE_ID		= 'luckycart/fields/store_id';
	const XML_PATH_LUCKYCART_FIELDS_TITLE			= 'luckycart/fields/title';
	const XML_PATH_LUCKYCART_FIELDS_NEW_CUSTOMER	= 'luckycart/fields/new_customer';
	const XML_PATH_LUCKYCART_FIELDS_SHIPPING		= 'luckycart/fields/shipping';
	const XML_PATH_LUCKYCART_FIELDS_DISCOUNT		= 'luckycart/fields/discount';
	const XML_PATH_LUCKYCART_FIELDS_GROUP			= 'luckycart/fields/group';
	const XML_PATH_LUCKYCART_FIELDS_AGE				= 'luckycart/fields/age';
	const XML_PATH_LUCKYCART_FIELDS_ADDRESS			= 'luckycart/fields/address';
	const XML_PATH_LUCKYCART_FIELDS_POSTCODE		= 'luckycart/fields/postcode';
	const XML_PATH_LUCKYCART_FIELDS_TOTAL_AMOUNT	= 'luckycart/fields/total_amount';
	const XML_PATH_LUCKYCART_FIELDS_ORDERS_COUNT	= 'luckycart/fields/orders_count';
	const XML_PATH_LUCKYCART_FIELDS_ORDER_STATUS	= 'luckycart/fields/order_status';
	const XML_PATH_LUCKYCART_FIELDS_IP_ADDRESS		= 'luckycart/fields/ip_address';
	const XML_PATH_LUCKYCART_CANCELATION_STATUS		= 'luckycart/cancelation/status';

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
    }
    
    
    public function getConfig($config_path)
	{
    return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
	}


    public function isEnabled()
    {
        return $this->getConfig(self::XML_PATH_LUCKYCART_GENERAL_ENABLE);
    }
    
    public function getApiKey()
    {
        return $this->getConfig(self::XML_PATH_LUCKYCART_API_KEY);
    }
    
    public function getApiSecret()
    {
        return $this->getConfig(self::XML_PATH_LUCKYCART_API_SECRET);
    }
    
    

}

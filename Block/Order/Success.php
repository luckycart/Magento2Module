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

namespace Yuukoo\Luckycart\Block\Order;

class Success extends \Magento\Checkout\Block\Onepage\Success
{

    /**
     * @var \Yuukoo\Luckycart\Helper\Data
     */
    protected $_helper;
    protected $_logger;

    private $orders;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Psr\Log\LoggerInterface $logger $logger
     * @param \Yuukoo\Luckycart\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        \Psr\Log\LoggerInterface $logger,
        \Yuukoo\Luckycart\Helper\Data $helper,
        array $data = []
    )
    {
        parent::__construct($context, $checkoutSession, $orderConfig, $httpContext, $data);
        $this->_helper = $helper;
        $this->_logger = $logger;
    }

    /**
     * create a new LuckyCart object and
     * init the plugin with an array of data to send to associate with the token
     * @access public
     * @return {string} luckyPlugin game
     */
    public function displayLuckyCart()
    {


        if (!$this->_helper->isEnabled())
            return false;

        try {
            $luckycart = new \LuckyCart($this->_helper->getApiKey(), $this->_helper->getApiSecret());
            $luckyPlugin = $luckycart->plugin($this->getPostData());

            if ($luckyPlugin) {
                return $luckyPlugin->gamediv . $luckyPlugin->script;
            }

        } catch (LuckyException $e) {

            $message = "LuckyCart plugin error : " . $e->getMessage();
            $this->_logger->debug($message);
        }
    }

    /*
        * getPostData
        *
        * returns an array of data to send to the LuckyCart API
        * @access private
        * @return {array} post data
        */

    private function getPostData()
    {


        $postdata = array(
            'customerId' => $this->getCustomerId(),
            'cartId' => $this->getOrderId(),
            'ttc' => $this->getCartValueTTC(),
            'ht' => $this->getCartValueHT(),
            'products' => $this->getOrderItems(),
            'currency' => $this->getOrder()->getOrderCurrencyCode(),
            'country' => $this->_helper->isEnabledInvoiceCountry() ? $this->getOrder()->getBillingAddress()->getCountryId() : '',
            'optin' => $this->_helper->isEnabledOptIn() ? $this->isCustomerOptin() : '',
            'nl' => $this->_helper->isEnabledOptIn() ? $this->isCustomerOptin() : '',
            'email' => $this->_helper->isEnabledEmail() ? $this->getOrder()->getCustomerEmail() : '',
            'payment' => $this->_helper->isEnabledPaymentMethod() ? $this->getPaymentMethod() : '',
            'firstName' => $this->_helper->isEnabledFirstname() ? $this->getOrder()->getBillingAddress()->getFirstname() : '',
            'lastName' => $this->_helper->isEnabledLastname() ? $this->getOrder()->getBillingAddress()->getLastname() : '',
            'codePromo' => $this->_helper->isEnabledDiscountCode() ? $this->getOrder()->getCouponCode() : '',
            'lang' => $this->_helper->isEnabledStoreLang() ? $this->getCurrentLang() : '',
            'shopId' => $this->_helper->isEnabledStoreId() ? $this->getOrder()->getStoreId() : '',
            'title' => $this->_helper->isEnabledCustomerTitle() ? $this->getCustomerGender() : '',
            'new' => $this->_helper->isEnabledNewCustomer() ? $this->isFirstPurchase() : '',
            'ship_ttc' => $this->_helper->isEnabledShipping() ? $this->getShippingTTC() : '',
            'ship_ht' => $this->_helper->isEnabledShipping() ? $this->getShippingHT() : '',
            'disc_ttc' => $this->_helper->isEnabledDiscount() ? $this->getDiscountAmountTTC() : '',
            'disc_ht' => $this->_helper->isEnabledDiscount() ? $this->getDiscountAmountHT() : '',
            'group' => $this->_helper->isEnabledGroup() ? $this->getOrder()->getCustomerGroupId() : '',
            'age' => $this->_helper->isEnabledAge() ? $this->getCustomerAge() : '',
            'address' => $this->_helper->isEnabledAddress() ? implode(" ", $this->getOrder()->getBillingAddress()->getStreet()) . " " . $this->getOrder()->getBillingAddress()->getPostcode() . " " . $this->getOrder()->getBillingAddress()->getCity() : '',
            'zip' => $this->_helper->isEnabledPostcode() ? $this->getOrder()->getBillingAddress()->getPostcode() : '',
            'total_ttc' => $this->_helper->isEnabledTotalAmount() ? $this->getTotalOrders() : '',
            'nb_cart' => $this->_helper->isEnabledOrdersCount() ? $this->getCustomerOrders()->getSize() : '',
            'status' => $this->_helper->isEnabledOrdersStatus() ? $this->getOrder()->getStatus() : '',
            'ip' => $this->_helper->isEnabledIpAddress() ? $this->getCustomerIp() : '',
        );

        return $postdata;
    }

    /*
     *
     * return Order object
     */

    private function getOrder()
    {
        return $this->_checkoutSession->getLastRealOrder();
    }

    /*
     * getOrderId
     *
     * returns the id of the current order
     * used to populate the post-data array
     * @access private
     * @return {integer} order-id
     */

    private function getOrderId()
    {
        return $this->getOrder()->getIncrementId();
    }


    /*
     * getCustomerId
     *
     */

    private function getCustomerId()
    {

        if ($this->getOrder()->getCustomerId() === NULL) {
            // If customer is a guest return G+OrderId
            return "G" . $this->getOrderId();
        }

        return $this->getOrder()->getCustomerId();
    }

    /*
    * getCustomerIp
    *
    * returns the ip of the current customer
    * used to populate the post-data array
    * @access private
    * @return {string} ip
    */

    private function getCustomerIp()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /*
    * getCurrentLang
    *
    * returns the iso code of the current store
    * used to populate the post-data array
    * @access private
    * @return {string} country iso
    */

    private function getCurrentLang()
    {
        $locale = new \Zend_Locale(
            \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Framework\App\Config\ScopeConfigInterface')
                ->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getOrder()->getStoreId()));

        return strtoupper($locale->getLanguage());
    }

    private function getPaymentMethod()
    {

        return $this->getOrder()->getPayment()->getMethodInstance()->getTitle();
    }

    private function getOrderItems()
    {
        $productsToOrder = array();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        foreach ($this->getOrder()->getAllVisibleItems() as $_item) {

            $product = $objectManager->create('Magento\Catalog\Model\Product')->load($_item->getProductId());

            $priceWithoutTax = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getBaseAmount();
            $priceWithTax = $product->getPriceInfo()->getPrice('final_price')->getAmount();

            $productsToOrder[] = array(
                'id' => $_item->getProductId(),
                'ttc' => $this->numberFormat($_item->getPriceInclTax()),
                'ht' => $this->numberFormat($_item->getPrice()),
                'qty' => (int)$_item->getQtyOrdered(),
                'cat' => implode(";", $product->getCategoryIds()),
                'brand' => ($product->getAttributeText($this->_helper->getSelectBrand())) ? $product->getAttributeText($this->_helper->getSelectBrand()) : '',
            );
        }

        return $productsToOrder;
    }

    private function getCartValueHT()
    {
        return $this->numberFormat($this->getOrder()->getGrandTotal() - $this->getOrder()->getTaxAmount());
    }

    private function getCartValueTTC()
    {
        return $this->numberFormat($this->getOrder()->getGrandTotal());
    }

    private function getShippingHT()
    {
        if ($this->_helper->getShippingTaxConfig()) {
            return $this->numberFormat($this->getOrder()->getShippingAmount());
        } else {
            return $this->numberFormat($this->getOrder()->getShippingAmount() - $this->getOrder()->getShippingTaxAmount());
        }
    }

    private function getShippingTTC()
    {
        if ($this->_helper->getShippingTaxConfig()) {
            return $this->numberFormat($this->getOrder()->getShippingAmount() + $this->getOrder()->getShippingTaxAmount());
        } else {
            return $this->numberFormat($this->getOrder()->getShippingAmount());
        }

    }

    private function getAverageTaxRate()
    {

        return $this->getOrder()->getGrandTotal() / ($this->getOrder()->getGrandTotal() - $this->getOrder()->getTaxAmount());
    }

    private function getDiscountAmountTTC()
    {
        if ($this->_helper->getDiscountTaxConfig()) {
            return $this->numberFormat(abs($this->getOrder()->getDiscountAmount()));
        } else {
            return $this->numberFormat(abs($this->getOrder()->getDiscountAmount()) * $this->getAverageTaxRate());
        }
    }

    private function getDiscountAmountHT()
    {
        if ($this->_helper->getDiscountTaxConfig()) {
            return $this->numberFormat(abs($this->getOrder()->getDiscountAmount()) / $this->getAverageTaxRate());
        } else {
            return $this->numberFormat(abs($this->getOrder()->getDiscountAmount()));
        }
    }

    private function getCustomerOrders()
    {
        if (empty($this->orders)) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $orderCollection = $objectManager->create('Magento\Sales\Model\ResourceModel\Order\CollectionFactory')
                ->create();


            $this->orders = $orderCollection
                ->addAttributeToFilter("customer_email", array($this->getOrder()->getCustomerEmail()))
                ->addAttributeToFilter('status', array('nin' => explode(',', $this->_helper->getCancelStatus())));
        }
        return $this->orders;
    }

    /*
         * isFirstPurchase
         *
         * returns true if this is a new customer
         * used to populate the post-data array
         * @access private
         * @return {bool}
         */

    private function isFirstPurchase()
    {

        if ($this->getCustomerOrders()->getSize() <= 1)
            return true;
        return 0;
    }

    private function getTotalOrders()
    {
        $total_ttc = 0;
        foreach ($this->getCustomerOrders() as $order) {
            $total_ttc += $order->getGrandTotal();
        }
        return $this->numberFormat($total_ttc);
    }

    private function isCustomerOptin()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $subscriberModel = $objectManager->create('Magento\Newsletter\Model\Subscriber')->loadByEmail($this->getOrder()->getCustomerEmail());

        if (null !== $subscriberModel->getId()) {
            return $subscriberModel->isSubscribed();
        }

        return 0;
    }

    private function getCustomerGender()
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customer = $objectManager->create('Magento\Customer\Model\Customer');

        $genderText = $customer->getResource()
            ->getAttribute('gender')
            ->getSource()
            ->getOptionText($this->getOrder()->getCustomerGender());

        return $genderText;
    }

    private function getCustomerAge()
    {

        $dob = new \DateTime($this->getOrder()->getCustomerDob());
        $now = new \DateTime();
        $age = $dob->diff($now);

        return $age->y;
    }


    private function numberFormat($price)
    {
        return number_format($price, 2, '.', '');
    }


}

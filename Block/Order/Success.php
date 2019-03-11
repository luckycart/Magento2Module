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

require_once("yuukoo/luckycart/luckycart.php");

class Success extends \Magento\Framework\View\Element\Template
{

	/**
     * @var \Yuukoo\Luckycart\Helper\Data
     */
    protected $_helper;


    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param \Ebizmarts\MailChimp\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Yuukoo\Luckycart\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_helper          = $helper;
    }
    
    
  /**
   * initPlugin
   *
   * create a new LuckyCart object and
   * init the plugin with an array of data to send to associate with the token
   * @access private
   * @return {object} luckyPlugin
   */

    private function initPlugin() {

        try {
            $luckycart = new LuckyCart(Mage::helper('luckycart')->getApiKey(), Mage::helper('luckycart')->getApiSecret());
            $luckyPlugin = $luckycart->plugin($this->getPostData());


        } catch (LuckyException $e) {
            Mage::log("LuckyCart plugin error : " . $e->getMessage());
            return false;
        }
        return $luckyPlugin;
    }

    /**
     * @return string
     */
    public function displayLuckyCart()
    {
    
     try {
            $luckycart = new LuckyCart(Mage::helper('luckycart')->getApiKey(), Mage::helper('luckycart')->getApiSecret());
         //   $luckyPlugin = $luckycart->plugin($this->getPostData());


        } catch (LuckyException $e) {
            Mage::log("LuckyCart plugin error : " . $e->getMessage());
            return false;
        }
        return $luckyPlugin;      
    }
}

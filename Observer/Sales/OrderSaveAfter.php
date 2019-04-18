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

namespace Yuukoo\Luckycart\Observer\Sales;

class OrderSaveAfter implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Yuukoo\Luckycart\Helper\Data
     */
    protected $_helper;
    protected $_logger;


    /**
     * @param \Yuukoo\Luckycart\Helper\Data $helper
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Yuukoo\Luckycart\Helper\Data $helper
    )
    {
        $this->_helper = $helper;
        $this->_logger = $logger;
    }
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        if (!$this->_helper->isEnabled())
            return false;

        $order = $observer->getEvent()->getOrder();



        if (in_array($order->getState(),explode(",",$this->_helper->getCancelStatus()))) {

            try {
                $luckycart = new \LuckyCart($this->_helper->getApiKey(), $this->_helper->getApiSecret());

                // Cancels the specified cart
                $cancel_info = $luckycart->cancel($order->getIncrementId());
                $history = $order->addStatusHistoryComment(__('<strong>LuckyCart:</strong> Cancelation of '. $cancel_info->tickets.' ticket(s) for Order #'.$cancel_info->id));
                $history->save();
                $order->save();

            } catch (LuckyException $e) {

                $message = "LuckyCart plugin error : " . $e->getMessage();
                $this->_logger->debug($message);
            }

        }

        return $this;
    }
}

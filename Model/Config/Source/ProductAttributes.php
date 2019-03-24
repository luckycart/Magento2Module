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

namespace Yuukoo\Luckycart\Model\Config\Source;

class ProductAttributes implements \Magento\Framework\Option\ArrayInterface
{
    const ALLOWED_TYPES = ['text', 'boolean', 'date', 'select', 'multiselect'];
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    private $attrCollection;
    /**
     * Constructor
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attrCollection
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attrCollection
    ) {
        $this->attrCollection = $attrCollection;
    }
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $attributes = $this->attrCollection->create()
            ->addVisibleFilter()
            //->addFieldToFilter('used_in_product_listing', 1)
            ->addFieldToFilter('frontend_input', ['in' => self::ALLOWED_TYPES])
            ->setOrder('frontend_label', 'asc')
            ->load();
        $optionArray = [];
        foreach ($attributes as $attribute) {
            $optionArray[] = [
                'label' => $attribute->getFrontendLabel(),
                'value' => $attribute->getAttributeCode()
            ];
        }
        return $optionArray;
    }
}

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


class Brand implements \Magento\Framework\Option\ArrayInterface
{

	private $attributecollectionFactory;
	
	public function __construct(\Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributecollectionFactory)
    {
    	$this->attributeCollectionFactory = $attributecollectionFactory;
    }
    
    protected function getAllAttributes()
    {
        $attributeCollection = $this->attributecollectionFactory->create();

        return $attributeCollection->getItems();
    }
    
    public function toOptionArray()
    {
        return [['value' => 'manufacturer', 'label' => __('manufacturer')]];
    }

    public function toArray()
    {
        return ['manufacturer' => __('manufacturer')];
    }
}

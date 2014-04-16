<?php
/**
 * @category   Calliweb
 * @package    Calliweb_Rma
 * @author	   Calliweb <office@calliweb.fr>
 * @copyright  Copyright (c) 2014 Calliweb (http://www.calliweb.fr)
 * @license    http://creativecommons.org/publicdomain/zero/1.0/ Creative Commons CC0
 */
class Calliweb_Rma_Block_View extends Calliweb_Rma_Block_Abstract
{
	/**
	 * Get Rma
	 *
	 * @return Calliweb_Rma_Model_Rma
	 */
	public function getRma()
	{
		return Mage::registry('current_rma');
	}

	/**
	 * Retrieve current Order
	 *
	 * @return Mage_Sales_Model_Order
	 */
	public function getOrder()
	{
		return Mage::registry('current_order');
	}
	
	/**
	 * Is current Order
	 *
	 * @param Mage_Sales_Model_Order
	 * @return bool
	 */
	public function isCurrentOrder(Mage_Sales_Model_Order $order)
	{
		return ($this->getOrder() && $this->getOrder()->getId() == $order->getId());
	}

	/**
	 * Can Order item be returned
	 *
	 * @param Mage_Sales_Model_Order_Item
	 * @return bool
	 */
	public function canReturnOrderItem(Mage_Sales_Model_Order_Item $item)
	{		
		return $this->helper('rma')->canReturnOrderItem($item);
	}

	/**
	 * Can Order item be displayed
	 *
	 * @param Mage_Sales_Model_Order_Item
	 * @return bool
	 */
	public function canDisplayOrderItem(Mage_Sales_Model_Order_Item $item)
	{
		if($item->isDummy(true))
			return false;
		
		if(!$item->isDummy()) {
			return $this->canReturnOrderItem($item);
		}
		
		foreach($item->getChildrenItems() as $child) {
			if($this->canReturnOrderItem($child)) return true;
		}
		return false;
	}
}
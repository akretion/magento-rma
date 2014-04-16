<?php
/**
 * @category   Calliweb
 * @package    Calliweb_Rma
 * @author	   Calliweb <office@calliweb.fr>
 * @copyright  Copyright (c) 2014 Calliweb (http://www.calliweb.fr)
 * @license    http://creativecommons.org/publicdomain/zero/1.0/ Creative Commons CC0
 */
class Calliweb_Rma_Model_Resource_Item_Collection extends Calliweb_Rma_Model_Resource_Collection_Abstract
{
	/**
	 * Define resource model
	 *
	 */
	protected function _construct()
	{
		$this->_init('rma/item');
	}

	/**
	 * Filter by Order Item
	 *
	 * @param Mage_Sales_Model_Order_Item
	 * @return Calliweb_Rma_Model_Resource_Item_Collection
	 */
	public function addOrderItemFilter(Mage_Sales_Model_Order_Item $order_item)
	{
		$this->addFieldToFilter('order_item_id', $order_item->getId());
		return $this;
	}
}
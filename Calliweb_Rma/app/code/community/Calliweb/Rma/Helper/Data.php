<?php
/**
 * @category   Calliweb
 * @package    Calliweb_Rma
 * @author	   Calliweb <office@calliweb.fr>
 * @copyright  Copyright (c) 2014 Calliweb (http://www.calliweb.fr)
 * @license    http://creativecommons.org/publicdomain/zero/1.0/ Creative Commons CC0
 */
class Calliweb_Rma_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Config Paths
	 */
	const CONFIG_PATH_ENABLED 		= 'rma/settings/enabled';
	const CONFIG_PATH_ORDER_STATES 	= 'rma/settings/order_states';
	const CONFIG_PATH_OBJECTS 		= 'rma/settings/objects';
	const CONFIG_PATH_DEFAULT_STATE	= 'rma/settings/default_state';
	
	/**
	 * Cache for returnable orders and order_items flags and qty
	 *
	 * @var array
	 */
	protected $_returnables = array();
	
	/**
	 * Is Module Enabled ?
	 *
	 * @return bool
	 */
	public function isRmaEnabled()
	{
		return Mage::getStoreConfigFlag(self::CONFIG_PATH_ENABLED);
	}
	
	/**
	 * Get allowed Order states
	 *
	 * @return array
	 */
	public function getAllowedOrderStates()
	{
		return explode(',', Mage::getStoreConfig(self::CONFIG_PATH_ORDER_STATES));
	}
	
	/**
	 * Get avalaible motivations for requests
	 *
	 * @return array
	 */
	public function getRequestObjects()
	{
		return preg_split("/\s*[\r\n]+\s*/", trim(Mage::getStoreConfig(self::CONFIG_PATH_OBJECTS)));
	}
	
	/**
	 * Get default RMA state
	 *
	 * @return string
	 */
	public function getDefaultState()
	{
		return trim(strip_tags(Mage::getStoreConfig(self::CONFIG_PATH_DEFAULT_STATE)));
	}
	
	/**
	 * Can Order be returned
	 *
	 * @param Mage_Sales_Model_Order
	 * @return bool
	 */
	public function canReturnOrder(Mage_Sales_Model_Order $order)
	{
		$key = 'order_'.$order->getId();
		if(!isset($this->_returnables[$key]))
		{
			$this->_returnables[$key] = false;
			
			// Module enabled && Order_state allowed ?
			if($this->isRmaEnabled()
			&& in_array($order->getState(), $this->getAllowedOrderStates())) {
				
				// Can return order items ?
				foreach($order->getItemsCollection() as $item) {
					if($this->canReturnOrderItem($item)) {
						$this->_returnables[$key] = true;
						break;
					}
				}
			}
		}
		return $this->_returnables[$key];
	}
	
	/**
	 * Can Order item be returned
	 *
	 * @param Mage_Sales_Model_Order_Item
	 * @return bool
	 */
	public function canReturnOrderItem(Mage_Sales_Model_Order_Item $item)
	{
		$key = 'order_item_'.$item->getId();
		if(!isset($this->_returnables[$key]))
		{
			$this->_returnables[$key] = false;

			if(!$item->isDummy() && $this->getQtyToReturn($item)) {
				$this->_returnables[$key] = true;
			}
		}
		return $this->_returnables[$key];
	}
	
	/**
	 * Get Qty to return
	 *
	 * @param Mage_Sales_Model_Order_Item
	 * @return float
	 */
	public function getQtyToReturn(Mage_Sales_Model_Order_Item $item)
	{
		if(null === $item->getQtyToReturn()) {
			$key = 'order_item_qty_to_return_'.$item->getId();
			if(!isset($this->_returnables[$key]))
			{
				if($qty = $item->getQtyShipped()) {
					$qty -= $this->getQtyReturned($item);
				}
				$this->_returnables[$key] = max(0, $qty);
			}
			$item->setQtyToReturn($this->_returnables[$key]);
		}
		return $item->getQtyToReturn();
	}
	
	/**
	 * Get Qty returned
	 *
	 * @param Mage_Sales_Model_Order_Item
	 * @return float
	 */
	public function getQtyReturned(Mage_Sales_Model_Order_Item $item)
	{
		if(null === $item->getQtyReturned()) {
			$key = 'order_item_qty_returned_'.$item->getId();
			if(!isset($this->_returnables[$key]))
			{
				$qty = 0;
				$rma_items = Mage::getResourceModel('rma/item_collection')
								 ->addOrderItemFilter($item);
				if($rma_items->getSize()) {
					foreach($rma_items as $rma_item) {
						$qty += $rma_item->getQty();
					}
				}
				$this->_returnables[$key] = max(0, $qty);
			}
			$item->setQtyReturned($this->_returnables[$key]);
		}
		return $item->getQtyReturned();
	}
}
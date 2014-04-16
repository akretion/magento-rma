<?php
/**
 * @category   Calliweb
 * @package    Calliweb_Rma
 * @author	   Calliweb <office@calliweb.fr>
 * @copyright  Copyright (c) 2014 Calliweb (http://www.calliweb.fr)
 * @license    http://creativecommons.org/publicdomain/zero/1.0/ Creative Commons CC0
 */
abstract class Calliweb_Rma_Block_Abstract extends Mage_Sales_Block_Items_Abstract
{
	/**
	 * Current Customer's Orders
	 *
	 * @var Mage_Sales_Model_Resource_Order_Collection
	 */
	protected $_orders = null;
	 
	/**
	 * Get current Customer
	 *
	 * @return Mage_Customer_Model_Customer
	 */
	protected function _getCustomer()
	{
		return Mage::getSingleton('customer/session')->getCustomer();	
	}
	
	/**
	 * Get current Customer's Orders
	 *
	 * @return Mage_Sales_Model_Resource_Order_Collection
	 */
	public function getOrders()
	{
		if(null === $this->_orders)
		{
			$this->_orders = Mage::getResourceModel('sales/order_collection')
							->addFieldToFilter('customer_id', $this->_getCustomer()->getId())
							->addFieldToFilter('state', array('in' => $this->helper('rma')->getAllowedOrderStates()))
							->load();
		}
		return $this->_orders;
	}
	
	/**
	 * Can current customer request for RMAs
	 *
	 * @return bool
	 */
	public function canReturnOrders()
	{
		foreach($this->getOrders() as $order) {
			if($this->canReturnOrder($order)) return true;
		}
		return false;
	}
	
	/**
	 * Can Order be returned
	 *
	 * @param Mage_Sales_Model_Order
	 * @return bool
	 */
	public function canReturnOrder(Mage_Sales_Model_Order $order)
	{
		return $this->helper('rma')->canReturnOrder($order);
	}
	
	/**
	 * Get Return Url
	 *
	 * @param Mage_Sales_Model_Order || null
	 * @return string
	 * @throw Mage_Exception
	 */
	public function getReturnUrl($order = null)
	{
		if(null !== $order && !$order instanceof Mage_Sales_Model_Order)
			Mage::throwException('Parameter must be an instance of Mage_Sales_Model_Order or null, '.gettype($order).' given');
		
		return $order ? $this->getUrl('rma/rma/new', array('order_id' => $order->getId()))
					  : $this->getUrl('rma/rma/new');
	}
	
	/**
	 * Get RMA Url
	 *
	 * @param Calliweb_Rma_Model_Rma || null
	 * @return string
	 * @throw Mage_Exception
	 */
	public function getRmaUrl($rma = null)
	{
		if(null !== $rma && !$rma instanceof Calliweb_Rma_Model_Rma)
			Mage::throwException('Parameter must be an instance of Calliweb_Rma_Model_Rma or null, '.gettype($order).' given');
		
		return $rma ? $this->getUrl('rma/rma/view', array('id' => $rma->getId()))
					: $this->getUrl('rma/rma/view');
	}
}
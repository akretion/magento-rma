<?php
/**
 * @category   Calliweb
 * @package    Calliweb_Rma
 * @author	   Calliweb <office@calliweb.fr>
 * @copyright  Copyright (c) 2014 Calliweb (http://www.calliweb.fr)
 * @license    http://creativecommons.org/publicdomain/zero/1.0/ Creative Commons CC0
 */
class Calliweb_Rma_Model_Item extends Mage_Core_Model_Abstract
{
	/**
	* Initialize resource model
	*/
	protected function _construct()
	{
		$this->_init('rma/item');
	}
	
	/**
	 * Get RMA
	 *
	 * @return Calliweb_Rma_Model_Rma
	 */
	public function getRma()
	{
		if(null === parent::getRma())
		{
			$rma = Mage::getModel('rma/rma')->load($this->getRmaId());
			$this->setRma($rma);
		}
		return parent::getRma();
	}
	
	/**
	 * Set RMA
	 *
	 * @param Calliweb_Rma_Model_Rma
	 * @return Calliweb_Rma_Model_Item
	 */
	public function setRma(Calliweb_Rma_Model_Rma $rma)
	{
		$this->setRmaId($rma->getId());
		return parent::setRma($rma);
	}
	
	/**
	 * Get Order item
	 *
	 * @return Mage_Sales_Model_Order_Item
	 */
	public function getOrderItem()
	{
		if(null === parent::getOrderItem())
		{
			$order_item = Mage::getModel('sales/order_item')->load($this->getOrderItemId());
			$this->setOrderItem($order_item);
		}
		return parent::getOrderItem();
	}
	
	/**
	 * Set Order item
	 *
	 * @param Mage_Sales_Model_Order_Item
	 * @return Calliweb_Rma_Model_Item
	 */
	public function setOrderItem(Mage_Sales_Model_Order_Item $order_item)
	{
		$order_item->setRmaItem($this);
		$this->setOrderItemId($order_item->getId());
		return parent::setOrderItem($order_item);
	}
	
	/**
	 * Get State
	 *
	 * @return string
	 */
	public function getState()
	{
		if(null === parent::getState())
		{
			$state = Mage::helper('rma')->getDefaultState();
			$this->setState($state);
		}
		return parent::getState();
	}
}
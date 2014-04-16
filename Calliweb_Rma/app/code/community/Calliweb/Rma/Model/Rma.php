<?php
/**
 * @category   Calliweb
 * @package    Calliweb_Rma
 * @author	   Calliweb <office@calliweb.fr>
 * @copyright  Copyright (c) 2014 Calliweb (http://www.calliweb.fr)
 * @license    http://creativecommons.org/publicdomain/zero/1.0/ Creative Commons CC0
 */
class Calliweb_Rma_Model_Rma extends Mage_Core_Model_Abstract
{	
	/**
	* Initialize resource model
	*/
	protected function _construct()
	{
		$this->_init('rma/rma');
	}
	
	/**
	 * Get Order
	 *
	 * @return Mage_Sales_Model_Order
	 */
	public function getOrder()
	{
		if(null === parent::getOrder())
		{
			$order = Mage::getModel('sales/order')->load($this->getOrderId());
			$this->setOrder($order);
		}
		return parent::getOrder();
	}
	
	/**
	 * Set Order
	 *
	 * @param Mage_Sales_Model_Order
	 * @return Calliweb_Rma_Model_Rma
	 */
	public function setOrder(Mage_Sales_Model_Order $order)
	{
		$this->setOrderId($order->getId());
		return parent::setOrder($order);
	}

	/**
	 * Get Customer
	 *
	 * @return Mage_Customer_Model_Customer
	 */
	public function getCustomer()
	{
		if(null === parent::getCustomer())
		{
			$customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
			$this->setCustomer($customer);
		}
		return parent::getCustomer();
	}
	
	/**
	 * Set Customer
	 *
	 * @param Mage_Customer_Model_Customer
	 * @return Calliweb_Rma_Model_Rma
	 */
	public function setCustomer(Mage_Customer_Model_Customer $customer)
	{
		$this->setCustomerId($customer->getId());
		return parent::setCustomer($customer);
	}

	/**
	 * Get RMA Items
	 *
	 * @return Calliweb_Rma_Model_Resource_Item_Collection
	 */
	public function getItems()
	{
		if(null === parent::getItems())
		{
			$items = Mage::getResourceModel('rma/item_collection')
						 ->addRmaFilter($this)
						 ->load();
			$this->setItems($items);
		}
		return parent::getItems();
	}
	
	/**
	 * Get RMA Attachments
	 *
	 * @return Calliweb_Rma_Model_Resource_Attachment_Collection
	 */
	public function getAttachments()
	{
		if(null === parent::getAttachments())
		{
			$attachments = Mage::getResourceModel('rma/attachment_collection')
								->addRmaFilter($this)
								->addChronologicalOrder()
								->load();
			$this->setAttachments($attachments);
		}
		return parent::getAttachments();
	}
	
	/**
	 * Get RMA Comments
	 *
	 * @return Calliweb_Rma_Model_Resource_Comment_Collection
	 */
	public function getComments()
	{
		if(null === parent::getComments())
		{
			$comments = Mage::getResourceModel('rma/comment_collection')
							->addRmaFilter($this)
							->addChronologicalOrder(Calliweb_Rma_Model_Resource_Comment_Collection::SORT_ORDER_ASC)
							->load();
			$this->setComments($comments);
		}
		return parent::getComments();
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
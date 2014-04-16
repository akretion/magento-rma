<?php
/**
 * @category   Calliweb
 * @package    Calliweb_Rma
 * @author	   Calliweb <office@calliweb.fr>
 * @copyright  Copyright (c) 2014 Calliweb (http://www.calliweb.fr)
 * @license    http://creativecommons.org/publicdomain/zero/1.0/ Creative Commons CC0
 */
abstract class Calliweb_Rma_Controller_Abstract extends Mage_Core_Controller_Front_Action
{
	/**
	 * Post attachments
	 *
	 * @var array
	 */
	protected $_attachments = null;
	
	/**
	 * Retrieve RMA helper
	 *
	 * @return Calliweb_Rma_Helper_Data
	 */
	protected function _getHelper()
	{
		return Mage::helper('rma');
	}

	/**
	 * Retrieve customer session model object
	 *
	 * @return Mage_Customer_Model_Session
	 */
	protected function _getSession()
	{
		return Mage::getSingleton('customer/session');
	}

	/**
	 * Get current Customer
	 *
	 * @return Mage_Customer_Model_Customer
	 */
	protected function _getCustomer()
	{
		return $this->_getSession()->getCustomer();	
	}
	
	/**
     * Init layout, messages and set active block for customer
	 */
	protected function _renderLayout()
	{
		$this->loadLayout();
        $this->_initLayoutMessages('customer/session');
		
		if($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
			$navigationBlock->setActive('rma/rma/list');
		}
		
		$this->renderLayout();
	}

	/**
	 * Validate Post
	 *
	 * @return bool
	 */
	protected function _validatePost()
	{
		return $this->_validateFormKey() && $this->getRequest()->isPost();
	}

	/**
	 * Get post attachments
	 *
	 * @return array
	 */
	protected function _getAttachments()
	{		
		if(null === $this->_attachments) {
			$this->_attachments = array();
			
			if(!isset($_FILES['attachments']))
				return $this->_attachments;
			
			$files = $_FILES['attachments'];
			if(!implode('', $files['name']))
				return $this->_attachments;
			
			foreach($files['name'] as $i => $name ) {
				if(!$name) continue;
				
				$this->_attachments[] = array(
					'name' => $name,
					'tmp_name' => $files['tmp_name'][$i],
					'mimetype' => $files['type'][$i]
				);
			}
		}
		return $this->_attachments;
	}

	/**
	 * Add attachments to RMA
	 *
	 * @throw Mage_Exception
	 */
	protected function _addAttachments()
	{
		if(!count($this->_getAttachments())) return;
		
		$rma = Mage::registry('current_rma');
		if(!$rma) Mage::throwException('Cannot add attachments because current RMA is not defined.');
		
		foreach($this->_getAttachments() as $data) {
			$attachment = Mage::getModel('rma/attachment')
						  ->setData($data)
						  ->setRma($rma)
						  ->setIsCustomer(1)
						  ->save();
		}
	}
	
	/**
	 * Check order view availability
	 *
	 * @param   Mage_Sales_Model_Order $order
	 * @return  bool
	 */
	protected function _canViewOrder($order)
	{
		$customerId = $this->_getCustomer()->getId();
		$availableStates = Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates();
		
		return ($order->getId() && $order->getCustomerId() && ($order->getCustomerId() == $customerId)
		&& in_array($order->getState(), $availableStates, $strict = true));
	}
	
	/**
	 * Try to load valid order by order_id and register it
	 *
	 * @param int $orderId
	 * @return bool
	 */
	protected function _loadValidOrder($orderId = null)
	{
		if (null === $orderId) {
			$orderId = (int) $this->getRequest()->getParam('order_id');
		}
		if (!$orderId) {
			return false;
		}
		
		$order = Mage::getModel('sales/order')->load($orderId);
		
		if ($this->_canViewOrder($order)) {
			Mage::register('current_order', $order);
			return true;
		}
		return false;
	}
	
	/**
	 * Action predispatch
	 *
	 * Check customer authentication & module activation
	 */
	public function preDispatch()
	{
		parent::preDispatch();
		
		// Module activation
		if(!$this->_getHelper()->isRmaEnabled()) {
			return $this->noRouteAction();	
		}
		
		// Customer authentication
		$loginUrl = Mage::helper('customer')->getLoginUrl();
		if(!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
			$this->setFlag('', self::FLAG_NO_DISPATCH, true);
		}
    }
}
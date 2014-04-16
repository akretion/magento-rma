<?php
/**
 * @category   Calliweb
 * @package    Calliweb_Rma
 * @author	   Calliweb <office@calliweb.fr>
 * @copyright  Copyright (c) 2014 Calliweb (http://www.calliweb.fr)
 * @license    http://creativecommons.org/publicdomain/zero/1.0/ Creative Commons CC0
 */
class Calliweb_Rma_RmaController extends Calliweb_Rma_Controller_Abstract
{
	/**
	 * List customer's RMAs
	 */
	public function listAction()
	{	
		$this->_renderLayout();
	}
	
	/**
	 * Create new RMA
	 */
	public function newAction()
	{
		$this->_loadValidOrder();
		$this->_renderLayout();
	}
	
	/**
	 * Create new RMA (submit)
	 */
	public function postAction()
	{	
		if(!$this->_validatePost()) {
			if(count($this->getRequest()->getParams())) {
				$this->_getSession()->addError($this->_getHelper()->__('An error occurs, please try again later.'));
			} else {
				$this->_getSession()->addError($this->_getHelper()->__('An error occurs, please try again without attachments. You will be able to add them later.'));	
			}
			return $this->_redirect('*/*/new');	
		}
		
		if(!$this->_loadValidOrder()) {
			$this->_getSession()->addError($this->_getHelper()->__('This order cannot be returned.'));
			return $this->_redirect('*/*/new');	
		}
		$order = Mage::registry('current_order');
		
		$data = new Varien_Object($this->getRequest()->getPost());
		$data->setOrder($order->getId());
				
		$errors = array();
		
		if(!in_array($data->getSubject(), $this->_getHelper()->getRequestObjects())) {
			$errors[] = $this->_getHelper()->__('Please, select an object for your request.');
		}
		
		$data->setDescription(trim(strip_tags($data->getDescription())));
		if(!$data->getDescription()) {
			$errors[] = $this->_getHelper()->__('Please, explain your motivations for your request.');
		}
		
		$items = $data->getData('order_'.$data->getOrder());
		if(!is_array($items)) {
			$errors[] = $this->_getHelper()->__('Please, select products to return.');
		} else {
			foreach($items as $id => $qty) {
				if(!$qty) unset($items[$id]);
			}
			if(!count($items)) {
				$errors[] = $this->_getHelper()->__('Please, select products to return.');
			} else {
				$ids = array_keys($items);
				$order_items = Mage::getResourceModel('sales/order_item_collection')
							->setOrderFilter($order)
							->addIdFilter(array('in' => $ids));
				if($order_items->getSize() != count($items)) {
					$errors[] = $this->_getHelper()->__('Please, select products to return.');
				} else {
					foreach($order_items as $item) {
						if(!$this->_getHelper()->canReturnOrderItem($item)) {
							$errors[] = $this->_getHelper()->__('%s cannot be returned.', $item->getName());
						} elseif($this->_getHelper()->getQtyToReturn($item) < $items[$item->getId()]) {
							$errors[] = $this->_getHelper()->__('Wrong quantity for %s.', $item->getName());							
						}
					}
				}
			}
		}
		
		if(count($errors)) {
			foreach($errors as $error) {
				$this->_getSession()->addError($error);
			}
			return $this->_redirect('*/*/new', array('id'=>$order->getId()));		
		}
		
		$rma = Mage::getModel('rma/rma')
					->setOrder($order)
					->setCustomer($this->_getCustomer())
					->setSubject($data->getSubject())
					->setDescription($data->getDescription());
		try {
			$rma->save();
			Mage::register('current_rma', $rma);
		} catch(Exception $e) {
			$this->_getSession()->addError($this->_getHelper()->__('An error occurs, please try again later.'));
			Mage::logException($e);
			return $this->_redirect('*/*/new', array('id'=>$order->getId()));			
		}
		
		foreach($items as $id => $qty) {
			$item = Mage::getModel('rma/item')
						->setRma($rma)
						->setOrderItemId($id)
						->setQty($qty);
			try {
				$item->save();
			} catch(Exception $e) {
				$rma->delete();
				$this->_getSession()->addError($this->_getHelper()->__('An error occurs, please try again later.'));
				Mage::logException($e);
				return $this->_redirect('*/*/new', array('id'=>$order->getId()));			
			}	
		}
				
		try {
			$this->_addAttachments();
		} catch(Exception $e) {
			$rma->delete();
			$this->_getSession()->addError($this->_getHelper()->__('An error occurs, please try again without attachments. You will be able to add them later.'));
			Mage::logException($e);
			return $this->_redirect('*/*/new', array('id'=>$order->getId()));			
		}

		$this->_getSession()->addSuccess($this->_getHelper()->__("Your request has been submited. We'll answer as soon as possible."));
        $this->_redirect('*/*/view', array('id'=>$rma->getId()));
	}
	
	/**
	 * View RMA
	 */
	public function viewAction()
	{
		if($id = $this->getRequest()->getParam('id')) {
			$rma = Mage::getModel('rma/rma')->load($id);
			if($rma->getCustomerId() == $this->_getCustomer()->getId()) {
				Mage::register('current_rma', $rma);
				Mage::register('current_order', $rma->getOrder());
				return $this->_renderLayout();
			}
		}
		$this->_redirect('*/*/list');
	}
}
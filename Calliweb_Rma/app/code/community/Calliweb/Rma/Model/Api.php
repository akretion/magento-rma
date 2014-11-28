<?php
/**
 * @category   Calliweb
 * @package    Calliweb_Rma
 * @author	   Calliweb <office@calliweb.fr>
 * @copyright  Copyright (c) 2014 Calliweb (http://www.calliweb.fr)
 * @license    http://creativecommons.org/publicdomain/zero/1.0/ Creative Commons CC0
 */
class Calliweb_Rma_Model_Api extends Mage_Api_Model_Resource_Abstract
{
	/**
	 * Format RMA
	 *
	 * @param Calliweb_Rma_Model_Rma
	 * @return array
	 */
	protected function _formatRma(Calliweb_Rma_Model_Rma $rma)
	{
		$result = $rma->toArray();
		$incrementId = $rma->getOrder()->getIncrementId();
		$result['order_increment_id'] = $incrementId;
		
		// Rma items
		$items = $rma->getItems();
		foreach($items as $item) {
			$result_item = $item->toArray();
			$order_item = $item->getOrderItem();
			$result_item['product_id'] = $order_item->getProductId();
			$result['items'][] = $result_item;
		}
		
		// Rma comments
		$comments = $rma->getComments();
		foreach($comments as $comment) {
			$result['comments'][] = $this->_formatComment($comment);	
		}
		
		// Rma attachments
		$attachments = $rma->getAttachments();
		foreach($attachments as $attachment) {
			$result['attachments'][] = $this->_formatAttachment($attachment);
		}

		return $result;	
	}
	
	/**
	 * Format RMA Comment
	 *
	 * @param Calliweb_Rma_Model_Comment
	 * @return array
	 */
	protected function _formatComment(Calliweb_Rma_Model_Comment $comment)
	{
		return $comment->toArray();
	}
	
	/**
	 * Format RMA Attachment
	 *
	 * @param Calliweb_Rma_Model_Attachment
	 * @return array
	 */
	protected function _formatAttachment(Calliweb_Rma_Model_Attachment $attachment)
	{
		$result = $attachment->toArray();
		$result['content'] = base64_encode($attachment->readFile());
		return $result;
	}
	
	/**
	 * List RMAs from date
	 *
	 * @param string
	 * @return array
	 */
	public function items($date = null)
	{
		$result = array();
		try {
			$rmas = Mage::getResourceModel('rma/rma_collection');
			
			if($date) {
				$date = new Zend_Date($date);
				$rmas->addDateFilter($date);
			}
			
			if($rmas->getSize()) {
				foreach($rmas as $rma) {
					$result[] = $this->_formatRma($rma);
				}
			}
		} catch (Exception $e) {
			Mage::logException($e);
			$this->_fault('error', $e->getMessage());
		}
		return $result;
	}
	
	/**
	 * Get RMA by id
	 *
	 * @param string
	 * @return array
	 */
	public function retrieve($id)
	{
		$result = array();
		try {
			$rma = Mage::getModel('rma/rma')->load($id);
			if($rma->getId()) {
				$result = $this->_formatRma($rma);
			}
		} catch (Exception $e) {
			Mage::logException($e);
			$this->_fault('error', $e->getMessage());
		}
		return $result;
	}
	
	/**
	 * Update RMA state by id
	 *
	 * @param string
	 * @param string
	 * @return array
	 */
	public function update($id, $state = null)
	{
		$result = array();
		try {
			$rma = Mage::getModel('rma/rma')->load($id);
			if($rma->getId()) {
				$rma->setState($state)->save();
				$result = $this->_formatRma($rma);
			}
		} catch (Exception $e) {
			Mage::logException($e);
			$this->_fault('error', $e->getMessage());
		}
		return $result;
	}
	
	/**
	 * List RMA comments from date
	 *
	 * @param string
	 * @param int
	 * @return array
	 */
	public function commentItems($date = null, $isCustomer = null)
	{
		$result = array();
		try {
			$comments = Mage::getResourceModel('rma/comment_collection');
			
			if($date) {
				$date = new Zend_Date($date);
				$comments->addDateFilter($date);
			}
			
			if(null !== $isCustomer) {
				$comments->addIsCustomerFilter($isCustomer);
			}
			
			if($comments->getSize()) {
				foreach($comments as $comment) {
					$result[] = $this->_formatComment($comment);
				}
			}
		} catch (Exception $e) {
			Mage::logException($e);
			$this->_fault('error', $e->getMessage());
		}
		return $result;
	}
	
	/**
	 * Create RMA comment from data
	 *
	 * @param data
	 * @return int
	 */
	public function commentCreate($data)
	{
		$result = array();
		try {
			$comment = Mage::getModel('rma/comment')
					 ->addData($data)
					 ->setIsCustomer(0)
					 ->save();
			$comment = $comment->load($comment->getId()); // force data refresh
			$result = $this->_formatComment($comment);
		} catch (Exception $e) {
			Mage::logException($e);
			$this->_fault('error', $e->getMessage());
		}
		return $result;
	}
	
	/**
	 * List RMA attachments from date
	 *
	 * @param string
	 * @param int
	 * @return array
	 */
	public function attachmentItems($date = null, $isCustomer = null)
	{
		$result = array();
		try {
			$attachments = Mage::getResourceModel('rma/attachment_collection');
			
			if($date) {
				$date = new Zend_Date($date);
				$attachments->addDateFilter($date);
			}
			
			if(null !== $isCustomer) {
				$attachments->addIsCustomerFilter($isCustomer);
			}
			
			if($attachments->getSize()) {
				foreach($attachments as $attachment) {
					$result[] = $this->_formatAttachment($attachment);
				}
			}
		} catch (Exception $e) {
			Mage::logException($e);
			$this->_fault('error', $e->getMessage());
		}
		return $result;
	}
	
	/**
	 * Create RMA attachment from data
	 *
	 * @param data
	 * @return int
	 */
	public function attachmentCreate($data)
	{
		$result = array();
		try {
			$attachment = Mage::getModel('rma/attachment')
					 ->addData($data)
					 ->setIsCustomer(0)
					 ->save();
			$attachment = $attachment->load($attachment->getId()); // force data refresh
			$result = $this->_formatAttachment($attachment);
		} catch (Exception $e) {
			Mage::logException($e);
			$this->_fault('error', $e->getMessage());
		}
		return $result;
	}
	
}

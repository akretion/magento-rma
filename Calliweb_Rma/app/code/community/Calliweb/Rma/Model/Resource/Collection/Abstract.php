<?php
/**
 * @category   Calliweb
 * @package    Calliweb_Rma
 * @author	   Calliweb <office@calliweb.fr>
 * @copyright  Copyright (c) 2014 Calliweb (http://www.calliweb.fr)
 * @license    http://creativecommons.org/publicdomain/zero/1.0/ Creative Commons CC0
 */
abstract class Calliweb_Rma_Model_Resource_Collection_Abstract extends Mage_Core_Model_Resource_Db_Collection_Abstract
{		
	/**
	 * Filter by RMA
	 *
	 * @param Calliweb_Rma_Model_Rma
	 * @return Calliweb_Rma_Model_Resource_Attachment_Collection
	 */
	public function addRmaFilter(Calliweb_Rma_Model_Rma $rma)
	{
		$this->addFieldToFilter('rma_id', $rma->getId());
		return $this;
	}
	
	/**
	 * Filter from Date
	 *
	 * @param Zend_Date
	 * @return Calliweb_Rma_Model_Resource_Rma_Collection
	 */
	public function addDateFilter(Zend_Date $date)
	{
		$date = $date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
		$this->addFieldToFilter('created_at', array('date' => array('from' => $date)));
		return $this;
	}
	
	/**
	 * Filter by customer added
	 *
	 * @param int
	 * @return Calliweb_Rma_Model_Resource_Comment_Collection
	 */
	public function addIsCustomerFilter($is_customer)
	{
		$this->addFieldToFilter('is_customer', (int) $is_customer);
		return $this;
	}
	
	/**
	 * Sort by chronological order
	 *
	 * @param string
	 * @return Calliweb_Rma_Model_Resource_Collection_Abstract
	 */
	public function addChronologicalOrder($direction = self::SORT_ORDER_DESC)
	{
		$this->setOrder('created_at', $direction);
		return $this;
	}
}
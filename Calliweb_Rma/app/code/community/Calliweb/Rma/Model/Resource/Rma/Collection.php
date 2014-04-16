<?php
/**
 * @category   Calliweb
 * @package    Calliweb_Rma
 * @author	   Calliweb <office@calliweb.fr>
 * @copyright  Copyright (c) 2014 Calliweb (http://www.calliweb.fr)
 * @license    http://creativecommons.org/publicdomain/zero/1.0/ Creative Commons CC0
 */
class Calliweb_Rma_Model_Resource_Rma_Collection extends Calliweb_Rma_Model_Resource_Collection_Abstract
{
	/**
	 * Define resource model
	 *
	 */
	protected function _construct()
	{
		$this->_init('rma/rma');
	}
	
	/**
	 * Filter by Customer
	 *
	 * @param Mage_Customer_Model_Customer
	 * @return Calliweb_Rma_Model_Resource_Rma_Collection
	 */
	public function addCustomerFilter(Mage_Customer_Model_Customer $customer)
	{
		$this->addFieldToFilter('customer_id', $customer->getId());
		return $this;
	}
}
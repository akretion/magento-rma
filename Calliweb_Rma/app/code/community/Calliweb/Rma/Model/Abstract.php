<?php
/**
 * @category   Calliweb
 * @package    Calliweb_Rma
 * @author	   Calliweb <office@calliweb.fr>
 * @copyright  Copyright (c) 2014 Calliweb (http://www.calliweb.fr)
 * @license    http://creativecommons.org/publicdomain/zero/1.0/ Creative Commons CC0
 */
abstract class Calliweb_Rma_Model_Abstract extends Mage_Core_Model_Abstract
{	
	/**
	 * Get addedBy
	 *
	 * @return string
	 */
	public function getAddedBy()
	{
		if(null === parent::getAddedBy()) {
			$addedBy = $this->getIsCustomer() ? Mage::helper('rma')->__('You')
											  : Mage::app()->getStore()->getFrontendName();
			$this->setAddedBy($addedBy);
		}
		return parent::getAddedBy();
	}
}
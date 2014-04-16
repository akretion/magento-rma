<?php
/**
 * @category   Calliweb
 * @package    Calliweb_Rma
 * @author	   Calliweb <office@calliweb.fr>
 * @copyright  Copyright (c) 2014 Calliweb (http://www.calliweb.fr)
 * @license    http://creativecommons.org/publicdomain/zero/1.0/ Creative Commons CC0
 */
class Calliweb_Rma_Block_List extends Calliweb_Rma_Block_Abstract
{
	/**
	 * Current customer's RMAs
	 *
	 * @var Calliweb_Rma_Model_Resource_Rma_Collection
	 */
	protected $_rmas = null;
	
	/**
	 * Get current customer's RMAs
	 *
	 * @return Calliweb_Rma_Model_Resource_Rma_Collection
	 */
	public function getRmas()
	{
		if(null === $this->_rmas)
		{
			$this->_rmas = Mage::getResourceModel('rma/rma_collection')
									->addCustomerFilter($this->_getCustomer())
									->addChronologicalOrder();
		}
		return $this->_rmas;
	}
	
	/**
	 * Configure pager block
	 *
	 * @return Calliweb_Rma_Block_List
	 */	
	public function _prepareLayout()
	{
		parent::_prepareLayout();
		
		$pager = $this->getLayout()
					  ->createBlock('page/html_pager', 'rma.list.pager')
					  ->setCollection($this->getRmas());
		$this->setChild('pager', $pager);
		$this->getRmas()->load();
		
		return $this;
	}
}
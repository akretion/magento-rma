<?php
/**
 * @category   Calliweb
 * @package    Calliweb_Rma
 * @author	   Calliweb <office@calliweb.fr>
 * @copyright  Copyright (c) 2014 Calliweb (http://www.calliweb.fr)
 * @license    http://creativecommons.org/publicdomain/zero/1.0/ Creative Commons CC0
 */
class Calliweb_Rma_Model_Comment extends Calliweb_Rma_Model_Abstract
{
	/**
	* Initialize resource model
	*/
	protected function _construct()
	{
		$this->_init('rma/comment');
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
	 * @return Calliweb_Rma_Model_Comment
	 */
	public function setRma(Calliweb_Rma_Model_Rma $rma)
	{
		$this->setRmaId($rma->getId());
		return parent::setRma($rma);
	}
}
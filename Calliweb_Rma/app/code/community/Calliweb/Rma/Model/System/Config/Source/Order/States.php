<?php
/**
 * @category   Calliweb
 * @package    Calliweb_Rma
 * @author	   Calliweb <office@calliweb.fr>
 * @copyright  Copyright (c) 2014 Calliweb (http://www.calliweb.fr)
 * @license    http://creativecommons.org/publicdomain/zero/1.0/ Creative Commons CC0
 */
class Calliweb_Rma_Model_System_Config_Source_Order_States
{	
	public function toOptionArray()
	{
		$options = array();
		foreach(Mage::getSingleton('sales/order_config')->getStates() as $value => $label) {
			$options[] = array('label' => $label, 
							   'value' => $value);
		}
		return $options;
	}
}
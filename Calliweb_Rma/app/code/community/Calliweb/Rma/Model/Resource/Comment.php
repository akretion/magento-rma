<?php
/**
 * @category   Calliweb
 * @package    Calliweb_Rma
 * @author	   Calliweb <office@calliweb.fr>
 * @copyright  Copyright (c) 2014 Calliweb (http://www.calliweb.fr)
 * @license    http://creativecommons.org/publicdomain/zero/1.0/ Creative Commons CC0
 */
class Calliweb_Rma_Model_Resource_Comment extends Calliweb_Rma_Model_Resource_Abstract
{
	/**
	 * Initialize resource model
	 */
	protected function _construct()
	{
		$this->_init('rma/comment', 'rma_comment_id');
	}
}
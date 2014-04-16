<?php
/**
 * @category   Calliweb
 * @package    Calliweb_Rma
 * @author	   Calliweb <office@calliweb.fr>
 * @copyright  Copyright (c) 2014 Calliweb (http://www.calliweb.fr)
 * @license    http://creativecommons.org/publicdomain/zero/1.0/ Creative Commons CC0
 */
class Calliweb_Rma_CommentController extends Calliweb_Rma_Controller_Abstract
{
	/**
	 * Add new Comment
	 */
	public function postAction()
	{
		if($this->_validatePost()) {
			$data = new Varien_Object($this->getRequest()->getParams());
			if($data->getId()) {
				$rma = Mage::getModel('rma/rma')->load($data->getId());
				Mage::register('current_rma', $rma);
				if($rma->getCustomerId() == $this->_getCustomer()->getId()) {
					
					$data->setMessage(trim(strip_tags($data->getMessage())));
					if($data->getMessage()) {
						$comment = Mage::getModel('rma/comment')
										->setRma($rma)
										->setMessage($data->getMessage())
										->setIsCustomer(1);
						try {
							$comment->save();
						} catch(Exception $e) {
							Mage::logException($e);
							$this->_getSession()->addError($this->_getHelper()->__('An error occurs, please try again.'));
						}
					}
					
					try {
						$this->_addAttachments();
					} catch(Exception $e) {
						Mage::logException($e);
						$this->_getSession()->addError($this->_getHelper()->__('An error occurs, please try again.'));
					}
					
					return $this->_redirect('rma/rma/view', array('id' => $data->getId()));
				}
			}
		}
		$this->_forward('noRoute');
	}
}
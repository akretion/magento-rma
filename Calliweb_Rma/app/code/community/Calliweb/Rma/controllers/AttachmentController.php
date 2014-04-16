<?php
/**
 * @category   Calliweb
 * @package    Calliweb_Rma
 * @author	   Calliweb <office@calliweb.fr>
 * @copyright  Copyright (c) 2014 Calliweb (http://www.calliweb.fr)
 * @license    http://creativecommons.org/publicdomain/zero/1.0/ Creative Commons CC0
 */
class Calliweb_Rma_AttachmentController extends Calliweb_Rma_Controller_Abstract
{
	/**
	 * Download attachment file
	 */
	public function downloadAction()
	{	
		if($id = $this->getRequest()->getParam('id')) {
			$attachment = Mage::getModel('rma/attachment')->load($id);
			if($attachment->getRmaId()) {
				$rma = $attachment->getRma();
				if($rma->getCustomerId() == $this->_getCustomer()->getId()) {
					return $this->getResponse()
								->setHeader('Content-Description', 'File Transfer', true)
								->setHeader('Content-Type', $attachment->getMimetype(false), true)
								->setHeader('Content-Disposition', 'attachment; filename='.$attachment->getName(false), true)
								->setHeader('Content-Transfer-Encoding', 'binary', true)
								->setBody($attachment->readFile());
				}
			}
		}
		$this->_forward('noRoute');
	}
}
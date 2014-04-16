<?php
/**
 * @category   Calliweb
 * @package    Calliweb_Rma
 * @author	   Calliweb <office@calliweb.fr>
 * @copyright  Copyright (c) 2014 Calliweb (http://www.calliweb.fr)
 * @license    http://creativecommons.org/publicdomain/zero/1.0/ Creative Commons CC0
 */
class Calliweb_Rma_Model_Attachment extends Calliweb_Rma_Model_Abstract
{	
	/**
	 * Initialize resource model
	 */
	protected function _construct()
	{
		$this->_init('rma/attachment');
	}
	
	/**
	 * Get Path
	 *
	 * @param bool
	 * @return string
	 */
	protected function _getPath($addFilename = false)
	{
		$path = Mage::getBaseDir('media') . DS . 'rma' . DS . 'attachments' . DS;
		if($addFilename) $path .= $this->getFilename();
		return $path;
	}
	
	/**
	 * File exists
	 *
	 * @return bool
	 * @throw Exception
	 */
	protected function _fileExists()
	{
		return is_file($this->_getPath(true));
	}
	
	/** 
	 * Write file
	 *
	 * @throw Exception
	 */
	protected function _writeFile()
	{
		// From user post
		if($this->getTmpName()) {
			$filename = md5(file_get_contents($this->getTmpName()));
			if(!move_uploaded_file($this->getTmpName(), $this->_getPath().$filename))
				Mage::throwException('Unabled to save uploaded file.');	
				
		// From API
		} elseif($this->getContent()) {
			$content = base64_decode($this->getContent());
			$filename = md5($content);
			if(file_put_contents($this->_getPath().$filename, $content) === false) {
				Mage::throwException('Unabled to save uploaded file.');						
			}
		} else {
			Mage::throwException('Unable to find file source.');	
		}
		
		// Delete old one if exists
		if($this->_fileExists()) {
			unlink($this->_getPath(true));
		}
		
		$this->setFilename($filename);
	}

	/**
	 * Create file if not exists
	 *
	 * @return Calliweb_Rma_Model_Attachment
	 * @throw Exception
	 */
	protected function _beforeSave()
	{
		if(!$this->_fileExists())
			$this->_writeFile();
		
		return parent::_beforeSave();	
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
	 * @return Calliweb_Rma_Model_Attachment
	 */
	public function setRma(Calliweb_Rma_Model_Rma $rma)
	{
		$this->setRmaId($rma->getId());
		return parent::setRma($rma);
	}
	
	/**
	 * Get Name
	 *
	 * @param bool
	 * @return string
	 */
	public function getName($strict = true)
	{
		if(!$strict && !parent::getName()) {
			return Mage::helper('rma')->__('File #%s', $this->getId());
		}
		return parent::getName();		
	}
	
	/**
	 * Read File
	 *
	 * @return string
	 */
	public function readFile()
	{
		return file_get_contents($this->_getPath(true));	
	}
}
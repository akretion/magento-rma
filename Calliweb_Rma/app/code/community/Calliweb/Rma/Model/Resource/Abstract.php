<?php
/**
 * @category   Calliweb
 * @package    Calliweb_Rma
 * @author	   Calliweb <office@calliweb.fr>
 * @copyright  Copyright (c) 2014 Calliweb (http://www.calliweb.fr)
 * @license    http://creativecommons.org/publicdomain/zero/1.0/ Creative Commons CC0
 */
abstract class Calliweb_Rma_Model_Resource_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Process model data before saving
     *
     * @param Mage_Core_Model_Abstract $model
     * @return Calliweb_Rma_Model_Resource_Abstract
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $model)
    {
        if($model->isObjectNew() && !$model->hasCreatedAt()) {
            $model->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }
		
        return parent::_beforeSave($model);
    }
}
<?php
/**
 * @category   Calliweb
 * @package    Calliweb_Rma
 * @author	   Calliweb <office@calliweb.fr>
 * @copyright  Copyright (c) 2014 Calliweb (http://www.calliweb.fr)
 * @license    http://creativecommons.org/publicdomain/zero/1.0/ Creative Commons CC0
 */
$this->startSetup();

/** RMA */

$table = $this->getConnection()
		->newTable($this->getTable('rma/rma'))
		->addColumn('rma_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'identity'  => true,
			'unsigned'  => true,
			'nullable'  => false,
			'primary'   => true
		))
		->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'unsigned'  => true,
			'nullable'  => false
		))
		->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'unsigned'  => true
		))
		->addColumn('subject', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
			'nullable'  => false
		))
		->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
			'nullable'  => false
		))
		->addColumn('state', Varien_Db_Ddl_Table::TYPE_TEXT, 255)
		->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP)
		->addIndex($this->getIdxName('rma/rma', array('order_id')), array('order_id'))
		->addIndex($this->getIdxName('rma/rma', array('customer_id')), array('customer_id'))
		->addForeignKey(
			$this->getFkName(
				'rma/rma',
				'order_id',
				'sales/order',
				'entity_id'
			),
			'order_id', $this->getTable('sales/order'), 'entity_id',
			Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
		->addForeignKey($this->getFkName('rma/rma', 'customer_id', 'customer/entity', 'entity_id'),
			'customer_id', $this->getTable('customer/entity'), 'entity_id',
			Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE);
$this->getConnection()->createTable($table);

/** RMA Item */

$table = $this->getConnection()
		->newTable($this->getTable(array('rma/rma', 'item')))
		->addColumn('rma_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'identity'  => true,
			'unsigned'  => true,
			'nullable'  => false,
			'primary'   => true
		))
		->addColumn('rma_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'unsigned'  => true,
			'nullable'  => false
		))
		->addColumn('order_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'unsigned'  => true,
			'nullable'  => false
		))
		->addColumn('qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
			'default'   => '0.0000',
		))
		->addColumn('state', Varien_Db_Ddl_Table::TYPE_TEXT, 255)
		->addIndex($this->getIdxName(array('rma/rma', 'item'), array('rma_id')), array('rma_id'))
		->addIndex($this->getIdxName(array('rma/rma', 'item'), array('order_item_id')), array('order_item_id'))
		->addForeignKey(
			$this->getFkName(
				array('rma/rma', 'item'),
				'rma_id',
				'rma/rma',
				'rma_id'
			),
			'rma_id', $this->getTable('rma/rma'), 'rma_id',
			Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
		->addForeignKey(
			$this->getFkName(
				array('rma/rma', 'item'),
				'order_item_id',
				array('sales/order', 'item'),
				'item_id'
			),
			'order_item_id', $this->getTable(array('sales/order', 'item')), 'item_id',
			Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);
$this->getConnection()->createTable($table);

$this->endSetup();

/** RMA Comment */

$table = $this->getConnection()
		->newTable($this->getTable(array('rma/rma', 'comment')))
		->addColumn('rma_comment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'identity'  => true,
			'unsigned'  => true,
			'nullable'  => false,
			'primary'   => true
		))
		->addColumn('rma_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'unsigned'  => true,
			'nullable'  => false
		))
		->addColumn('message', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
			'nullable'  => false
		))
		->addColumn('is_customer', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
			'unsigned'  => true
		))
		->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP)
		->addIndex($this->getIdxName(array('rma/rma', 'item'), array('rma_id')), array('rma_id'))
		->addIndex($this->getIdxName(array('rma/rma', 'item'), array('created_at')), array('created_at'))
		->addForeignKey(
			$this->getFkName(
				array('rma/rma', 'comment'),
				'rma_id',
				'rma/rma',
				'rma_id'
			),
			'rma_id', $this->getTable('rma/rma'), 'rma_id',
			Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);
$this->getConnection()->createTable($table);

/** RMA Attachment */

$table = $this->getConnection()
		->newTable($this->getTable(array('rma/rma', 'attachment')))
		->addColumn('rma_attachment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'identity'  => true,
			'unsigned'  => true,
			'nullable'  => false,
			'primary'   => true
		))
		->addColumn('rma_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'unsigned'  => true,
			'nullable'  => false
		))
		->addColumn('filename', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
			'nullable'  => false
		))
		->addColumn('mimetype', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
			'nullable'  => false
		))
		->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
			'nullable'  => true
		))
		->addColumn('is_customer', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
			'unsigned'  => true
		))
		->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP)
		->addIndex($this->getIdxName(array('rma/rma', 'item'), array('rma_id')), array('rma_id'))
		->addIndex($this->getIdxName(array('rma/rma', 'item'), array('created_at')), array('created_at'))
		->addForeignKey(
			$this->getFkName(
				array('rma/rma', 'attachment'),
				'rma_id',
				'rma/rma',
				'rma_id'
			),
			'rma_id', $this->getTable('rma/rma'), 'rma_id',
			Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);
$this->getConnection()->createTable($table);

/** Here's a script to delete the module in database

DROP TABLE IF EXISTS `rma_attachment`;
DROP TABLE IF EXISTS `rma_comment`;
DROP TABLE IF EXISTS `rma_item`;
DROP TABLE IF EXISTS `rma`;
DELETE FROM `core_resource` WHERE `code` = 'rma_setup';
DELETE FROM `core_config_data` WHERE `path` LIKE 'rma/%';

*/
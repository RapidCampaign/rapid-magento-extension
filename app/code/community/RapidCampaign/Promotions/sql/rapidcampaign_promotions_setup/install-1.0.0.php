<?php
/**
 * RapidCampaign
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * Drop table if exist
 */
$installer->getConnection()->dropTable($this->getTable('rapidcampaign_promotions/promotions'));

/**
 * Create table 'rapidcampaign_promotions'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('rapidcampaign_promotions/promotions'))
    ->addColumn('slug', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        'primary'   => true
    ), 'Promotion Slug')
    ->addColumn('locale', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        'default'   => 'en_GB'
    ), 'Promotion Locale')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        'default'   => ''
    ), 'Promotions Name')
    ->addColumn('width', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0'
    ), 'Promotion Width')
    ->addColumn('height', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0'
    ), 'Promotion Height')
    ->addColumn('expire_time', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'unsigned'  => true,
        'nullable'  => false
    ), 'Expire Time');

$installer->getConnection()->createTable($table);

$installer->endSetup();

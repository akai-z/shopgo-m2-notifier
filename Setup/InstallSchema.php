<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace ShopGo\Notifier\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'shopgo_notifier_notification'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('shopgo_notifier_notification')
        )->addColumn(
            'notification_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Notification ID'
        )->addColumn(
            'identifier',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Notification Identifier'
        )->addColumn(
            'notification_inbox_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default'  => '0'],
            'Notification Inbox ID'
        )->addForeignKey(
            $installer->getFkName('shopgo_notifier_notification', 'notification_inbox_id', 'adminnotification_inbox', 'notification_id'),
            'notification_inbox_id',
            $installer->getTable('adminnotification_inbox'),
            'notification_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('shopgo_notifier_notification'),
                ['identifier', 'notification_inbox_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['identifier', 'notification_inbox_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->setComment(
            'ShopGo Notifier Notification Table'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}

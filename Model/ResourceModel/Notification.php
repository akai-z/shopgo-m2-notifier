<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\Notifier\Model\ResourceModel;

/**
 * Notifier notification model
 */
class Notification extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('shopgo_notifier_notification', 'notification_id');
    }

    /**
     * Load an object using 'identifier' field if there's no field specified and value is not numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param mixed $value
     * @param string $field
     * @return $this
     */
    public function load(
        \Magento\Framework\Model\AbstractModel $object,
        $value,
        $field = null
    ) {
        if (!is_numeric($value) && $field === null) {
            $field = 'identifier';
        }

        return parent::load($object, $value, $field);
    }

    /**
     * Load an object using 'notification_inbox_id' field
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param int $id
     * @return $this
     */
    public function loadByNotificationInboxId(
        \Magento\Framework\Model\AbstractModel $object,
        $id
    ) {
        return parent::load($object, $id, 'notification_inbox_id');
    }
}

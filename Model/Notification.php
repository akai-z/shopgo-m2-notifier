<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\Notifier\Model;

use Magento\Framework\Notification\MessageInterface;

class Notification extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Notification inbox model
     *
     * @var \Magento\AdminNotification\Model\Inbox
     */
    protected $_notificationInbox;

    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\AdminNotification\Model\Inbox $notificationInbox
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\AdminNotification\Model\Inbox $notificationInbox,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_notificationInbox = $notificationInbox;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $registry);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ShopGo\Notifier\Model\ResourceModel\Notification');
    }

    /**
     * Get notification model
     *
     * @return \ShopGo\Notifier\Model\Notification
     */
    private function _getModel()
    {
        return $this->_objectManager->create('ShopGo\Notifier\Model\Notification');
    }

    /**
     * Get notification inbox ID
     *
     * @param string $identifier
     * @return int
     */
    protected function _getNotificationInboxId($identifier)
    {
        return $this->_getModel()->load($identifier)
            ->getNotificationInboxId();
    }

    /**
     * Set notification inbox ID
     *
     * @param string $identifier
     * @param string $notificationInboxId
     * @return string
     */
    protected function _setNotificationInboxId($identifier, $notificationInboxId)
    {
        $this->_getModel()->setIdentifier($identifier)
            ->setNotificationInboxId($notificationInboxId)
            ->save();

        return __('Notification ID has been set!');
    }

    /**
     * Check whether notification is new
     *
     * @param string $identifier
     * @return bool
     */
    public function isNewNotification($identifier)
    {
        $notificationId = $this->_getModel()->getCollection()
            ->addFieldToFilter('identifier', $identifier)
            ->getFirstItem()
            ->getId();

        return $notificationId ? false : true;
    }

    /**
     * Add notification
     *
     * @param int $severity
     * @param string $identifier
     * @param string $title
     * @param string|string[] $description
     * @param string $url
     * @param bool $isInternal
     * @return boolean
     */
    public function addNotification($severity, $identifier, $title, $description, $url = '', $isInternal = true)
    {
        $result = false;

        if (!$this->isNewNotification($identifier)) {
            return $result;
        }

        switch ($severity) {
            case MessageInterface::SEVERITY_CRITICAL:
                $this->_notificationInbox->addCritical($title, $description, $url, $isInternal);
                $result = true;
                break;
            case MessageInterface::SEVERITY_MAJOR:
                $this->_notificationInbox->addMajor($title, $description, $url, $isInternal);
                $result = true;
                break;
            case MessageInterface::SEVERITY_MINOR:
                $this->_notificationInbox->addMinor($title, $description, $url, $isInternal);
                $result = true;
                break;
            case MessageInterface::SEVERITY_NOTICE:
                $this->_notificationInbox->addNotice($title, $description, $url, $isInternal);
                $result = true;
                break;
            default:
                $this->_notificationInbox->add($severity, $title, $description, $url, $isInternal);
                $result = true;
        }

        if ($result) {
            $notice = $this->_notificationInbox->loadLatestNotice();
            $this->_setNotificationInboxId($identifier, $notice->getNotificationId());
        }

        return $result;
    }

    /**
     * Delete notifier notification by identifier
     *
     * @param string $identifier
     * @return boolean
     */
    public function deleteByIdentifier($identifier)
    {
        $model = $this->_getModel()->load($identifier);
        $model->delete();

        return true;
    }

    /**
     * Remove notification
     *
     * @param string $identifier
     * @return boolean
     */
    public function removeNotification($identifier)
    {
        $result = false;
        $notificationInboxId = $this->_getNotificationInboxId($identifier);

        if ($notificationInboxId) {
            $notification = $this->_notificationInbox->load($notificationInboxId);

            $notification->setIsRemove(1)->save();
            //@TODO: Loading the same notification again.
            //Not a pretty sight! Might change it later.
            $this->deleteByIdentifier($identifier);

            $result = true;
        }

        return $result;
    }
}

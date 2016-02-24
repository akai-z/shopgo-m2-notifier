<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\Notifier\Model\System\Message;

class Notifications implements \Magento\Framework\Notification\MessageInterface
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var \Magento\AdminNotification\Model\Inbox
     */
    protected $_notificationInbox;

    /**
     * @var \ShopGo\Notifier\Model\Notification
     */
    protected $_notification;

    /**
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\AdminNotification\Model\Inbox $notificationInbox
     * @param \ShopGo\Notifier\Model\Notification $notification
     */
    public function __construct(
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\AdminNotification\Model\Inbox $notificationInbox,
        \ShopGo\Notifier\Model\Notification $notification
    ) {
        $this->_authorization = $authorization;
        $this->_urlBuilder = $urlBuilder;
        $this->_notificationInbox = $notificationInbox;
        $this->_notification = $notification;
    }

    /**
     * Get array of cache types which require data refresh
     *
     * @return array
     */
    protected function _getNotificationsIdentifiers()
    {
        $identifiers = [];
        foreach ($this->_notification->getCollection() as $notification) {
            $identifiers[] = $notification->getIdentifier();
        }

        return $identifiers;
    }

    /**
     * Check whether there are important notifications
     *
     * @return bool
     */
    protected function _isImportant()
    {
        foreach ($this->_notification->getCollection() as $notification) {
            $inboxNotification = $this->_notificationInbox->load(
                $notification->getNotificationInboxId()
            );

            $severity = $inboxNotification->getSeverity();

            if ($severity !== null && $severity < 3) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return md5('notification' . implode(':', $this->_getNotificationsIdentifiers()));
    }

    /**
     * Check whether message can be displayed
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->_authorization->isAllowed(
            'Magento_AdminNotification::show_list'
        ) && $this->_isImportant();
    }

    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        $message = __(
            'There are some important notifications that you must check.'
            . ' Please go to <a href="%1">Notifications Page</a>. Or, check notifications icon.',
            $this->getLink()
        );

        return $message;
    }

    /**
     * Retrieve problem management url
     *
     * @return string|null
     */
    public function getLink()
    {
        return $this->_urlBuilder->getUrl('adminhtml/notification');
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return \Magento\Framework\Notification\MessageInterface::SEVERITY_CRITICAL;
    }
}

<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\Notifier\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Sns notification observer
 */
class SnsNotification implements ObserverInterface
{
    /**
     * Notifier code
     */
    const CODE = 'ShopGo_Notifier';

    /**
     * Add notification action
     */
    const ACTION_ADD_NOTIFICATION = 'add_notification';

    /**
     * Remove notification action
     */
    const ACTION_REMOVE_NOTIFICATION = 'remove_notification';

    /**
     * Notifier model
     *
     * @var \ShopGo\Notifier\Model\Notification
     */
    protected $_notification;

    /**
     * @param \ShopGo\Notifier\Model\Notification $notification
     */
    public function __construct(\ShopGo\Notifier\Model\Notification $notification)
    {
        $this->_notification = $notification;
    }

    /**
     * Handle SNS notifications
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $notification = $observer->getEvent()->getData('notification');

        if ($notification['module'] != self::CODE) {
            return false;
        }

        switch ($notification['action']) {
            case self::ACTION_ADD_NOTIFICATION:
                $url = isset($notification['arguments']['url'])
                    ? $notification['arguments']['url']
                    : '';

                $isInternal = isset($notification['arguments']['is_internal'])
                    ? (bool) $notification['arguments']['is_internal']
                    : true;

                $this->_notification->addNotification(
                    $notification['arguments']['severity'],
                    $notification['arguments']['name'],
                    $notification['arguments']['title'],
                    $notification['arguments']['description'],
                    $url,
                    $isInternal
                );
                break;
            case self::ACTION_REMOVE_NOTIFICATION:
                $this->_notification->removeNotification(
                    $notification['arguments']['name']
                );
                break;
        }
    }
}

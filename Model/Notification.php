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
     * Notification xml path
     */
    const XML_PATH_NOTIFICATION = 'notifier/notification';

    /**
     * Config factory model
     *
     * @var \Magento\Config\Model\Config\Factory
     */
    protected $_configFactory;

    /**
     * App state
     *
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * Notification inbox model
     *
     * @var \Magento\AdminNotification\Model\Inbox
     */
    protected $_notificationInbox;

    /**
     * @param \Magento\Config\Model\Config\Factory $configFactory
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\AdminNotification\Model\Inbox $notificationInbox
     */
    public function __construct(
        \Magento\Config\Model\Config\Factory $configFactory,
        \Magento\Framework\App\State $appState,
        \Magento\AdminNotification\Model\Inbox $notificationInbox
    ) {
        $this->_configFactory = $configFactory;
        $this->_appState = $appState;
        $this->_notificationInbox = $notificationInbox;
    }

    /**
     * Get config model
     *
     * @param array $configData
     * @return \Magento\Config\Model\Config
     */
    protected function _getConfigModel($configData = [])
    {
        /** @var \Magento\Config\Model\Config $configModel  */
        $configModel = $this->_configFactory->create(['data' => $configData]);
        return $configModel;
    }

    /**
     * Get config data value
     *
     * @param string $path
     * @return string
     */
    protected function _getConfigData($path)
    {
        return $this->_getConfigModel()->getConfigDataValue($path);
    }

    /**
     * Set config data
     *
     * @param array $configData
     */
    protected function _setConfigData($configData = [])
    {
        $this->_getConfigModel($configData)->save();
    }

    /**
     * Get notification ID
     *
     * @param string $notificationName
     * @return string
     */
    public function getNotificationId($notificationName)
    {
        return $this->_getConfigModel()->getConfigDataValue(
            self::XML_PATH_NOTIFICATION . '/' . $notificationName
        );
    }

    /**
     * Set notification ID
     *
     * @param string $notificationName
     * @param string $notificationId
     * @return string
     */
    public function setNotificationId($notificationName, $notificationId)
    {
        $result = __('Could not set notification ID!');

        try {
            $group = [
                'notification' => [
                    'fields' => [
                        $notificationName => [
                            'value' => $notificationId
                        ]
                    ]
                ]
            ];

            $configData = [
                'section' => 'notifier',
                'website' => null,
                'store'   => null,
                'groups'  => $group
            ];

            $this->_setConfigData($configData);

            $result = __('Notification ID has been set!');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $messages = explode("\n", $e->getMessage());

            foreach ($messages as $message) {
                $result .= "\n" . $message;
            }
        } catch (\Exception $e) {
            $result .= "\n" . $e->getMessage();
        }

        return $result;
    }

    /**
     * Add notification
     *
     * @param int $severity
     * @param string $name
     * @param string $title
     * @param string|string[] $description
     * @param string $url
     * @param bool $isInternal
     * @return boolean
     */
    public function addNotification($severity, $name, $title, $description, $url = '', $isInternal = true)
    {
        $result = false;
        
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
            $this->setNotificationId($name, $notice->getNotificationId());
        }
        
        return $result;
    }

    /**
     * Remove notification
     *
     * @param string $notificationName
     * @return boolean
     */
    public function removeNotification($notificationName)
    {
        $result = false;
        $notificationId = $this->getNotificationId($notificationName);

        if ($notificationId) {
            $notification = $this->_notificationInbox->load($notificationId);

            $notification->setIsRemove(1)->save();
            $this->setNotificationId($notificationName, 0);

            $result = true;
        }
        
        return $result;
    }

    /**
     * Set area code
     *
     * @param string $code
     */
    public function setAreaCode($code)
    {
        $this->_appState->setAreaCode($code);
    }
}

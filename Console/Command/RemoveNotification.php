<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\Notifier\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ShopGo\Notifier\Model\Notification;

/**
 * Remove notification
 */
class RemoveNotification extends Command
{
    /**
     * Name argument
     */
    const NAME_ARGUMENT = 'name';

    /**
     * @var Notification
     */
    private $_notification;

    /**
     * @param Notification $notification
     */
    public function __construct(Notification $notification)
    {
        parent::__construct();
        $this->_notification = $notification;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('notifier:remove-notification')
            ->setDescription('Remove notification')
            ->setDefinition([
                new InputArgument(
                    self::NAME_ARGUMENT,
                    null,
                    InputArgument::REQUIRED,
                    'Name'
                )
            ]);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $result = 'Could not remove notification!';
        
        $name  = $input->getArgument(self::NAME_ARGUMENT);

        if (!is_null($name)) {
            $this->_notification->setAreaCode('adminhtml');

            $result = $this->_notification->removeNotification($name);

            $result = $result
                ? "Notification '{$name}' has been remove!"
                : "Could not remove notification '{$name}'!";
        } else {
            throw new \InvalidArgumentException('Argument ' . self::NAME_ARGUMENT . ' is missing.');
        }

        $output->writeln('<info>' . $result . '</info>');
    }
}

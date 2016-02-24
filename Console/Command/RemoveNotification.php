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
     * Identifier argument
     */
    const IDENTIFIER_ARGUMENT = 'identifier';

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
                    self::IDENTIFIER_ARGUMENT,
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
        $identifier = $input->getArgument(self::IDENTIFIER_ARGUMENT);

        if (!is_null($identifier)) {
            $result = $this->_notification->removeNotification($identifier);
            $result = $result
                ? "Notification '{$identifier}' has been remove!"
                : "Could not remove notification '{$identifier}'!";
        } else {
            throw new \InvalidArgumentException('Argument ' . self::IDENTIFIER_ARGUMENT . ' is missing.');
        }

        $output->writeln('<info>' . $result . '</info>');
    }
}

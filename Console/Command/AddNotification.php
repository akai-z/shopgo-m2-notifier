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
 * Add notification
 */
class AddNotification extends Command
{
    /**
     * Severity argument
     */
    const SEVERITY_ARGUMENT = 'severity';

    /**
     * Identifier argument
     */
    const IDENTIFIER_ARGUMENT = 'identifier';

    /**
     * Title argument
     */
    const TITLE_ARGUMENT = 'title';

    /**
     * Description argument
     */
    const DESCRIPTION_ARGUMENT = 'description';

    /**
     * URL argument
     */
    const URL_ARGUMENT = 'url';

    /**
     * Is Internal argument
     */
    const IS_INTERNAL_ARGUMENT = 'is_internal';

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
        $options = [
            new InputArgument(
                self::SEVERITY_ARGUMENT,
                null,
                InputArgument::REQUIRED,
                'Severity'
            ),
            new InputArgument(
                self::IDENTIFIER_ARGUMENT,
                null,
                InputArgument::REQUIRED,
                'Identifier'
            ),
            new InputArgument(
                self::TITLE_ARGUMENT,
                null,
                InputArgument::REQUIRED,
                'Title'
            ),
            new InputArgument(
                self::DESCRIPTION_ARGUMENT,
                null,
                InputArgument::REQUIRED,
                'Description'
            ),
            new InputArgument(
                self::URL_ARGUMENT,
                null,
                InputArgument::OPTIONAL,
                'URL'
            ),
            new InputArgument(
                self::IS_INTERNAL_ARGUMENT,
                null,
                InputArgument::OPTIONAL,
                'Is Internal'
            )
        ];

        $this->setName('notifier:add-notification')
            ->setDescription('Add notification')
            ->setDefinition($options);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $severity    = $input->getArgument(self::SEVERITY_ARGUMENT);
        $identifier  = $input->getArgument(self::IDENTIFIER_ARGUMENT);
        $title       = $input->getArgument(self::TITLE_ARGUMENT);
        $description = $input->getArgument(self::DESCRIPTION_ARGUMENT);
        $url         = $input->getArgument(self::URL_ARGUMENT);
        $isInternal  = $input->getArgument(self::IS_INTERNAL_ARGUMENT);

        if (
            !is_null($severity)
            && !is_null($identifier)
            && !is_null($title)
            && !is_null($description)
        ) {
            $result = $this->_notification->addNotification(
                $severity, $identifier,
                $title, $description,
                $url, $isInternal
            );

            $result = $result
                ? "Notification '{$identifier}' has been added!"
                : "Could not add notification '{$identifier}'!";
        } else {
            throw new \InvalidArgumentException('Missing arguments.');
        }

        $output->writeln('<info>' . $result . '</info>');
    }
}

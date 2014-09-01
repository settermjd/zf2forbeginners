<?php
/**
 * Created by PhpStorm.
 * User: mattsetter
 * Date: 31/08/14
 * Time: 21:06
 */

namespace BabyMonitor\Notify\Feed;

use BabyMonitor\Model\FeedModel;
use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;

class EmailNotifier implements NotifyInterface
{
    const DEFAULT_SUBJECT = 'Baby Monitor Feed Notification';
    const NOTIFY_CREATE = 'create';
    const NOTIFY_UPDATE = 'update';
    const NOTIFY_DELETE = 'delete';

    protected $_config;
    protected $_mailTransport;

    public function __construct($emailConfig, TransportInterface $mailTransport)
    {
        if (empty($emailConfig)) {
            throw new EmailNotifierException(
                'Missing notifier configuration data'
            );
        }

        $this->_config = $emailConfig;
        $this->_mailTransport = $mailTransport;
    }

    public function notify(FeedModel $feed, $notificationType)
    {
        if (empty($this->_config['subject'])) {
            $subject = self::DEFAULT_SUBJECT;
        } else {
            $subject = $this->_config['subject'];
        }

        $message = new Message();
        $message->setBody(
            $this->getNotificationBody(
                $feed, $notificationType
            )
        )
            ->setSubject($subject)
            ->addFrom($this->_config['address']['from'])
            ->addTo($this->_config['address']['to']);

        return $this->_mailTransport->send($message);
    }

    public function getNotificationBody(FeedModel $feed, $notificationType)
    {
        switch ($notificationType) {
            case (self::NOTIFY_UPDATE):
                $message = sprintf(
                    "Feed %d has been updated", $feed->feedId
                );
                break;

            case (self::NOTIFY_DELETE):
                $message = sprintf(
                    "Feed %d has been deleted", $feed->feedId
                );
                break;

            case (self::NOTIFY_CREATE):
            default:
                $message = sprintf(
                    "Feed has been created. Id is: %d", $feed->feedId
                );
                break;
        }

        return $message;
    }
} 
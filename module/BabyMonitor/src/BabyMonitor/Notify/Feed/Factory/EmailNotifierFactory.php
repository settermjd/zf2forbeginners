<?php
/**
 * Created by PhpStorm.
 * User: mattsetter
 * Date: 31/08/14
 * Time: 21:10
 */

namespace BabyMonitor\Notify\Feed\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\Cache\Exception\ExtensionNotLoadedException;
use BabyMonitor\Notify\Feed\EmailNotifier;

class EmailNotifierFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $emailConfig = '';

        if (array_key_exists('notification', $config)) {
            $emailConfig = $config['notification'];
        }

        $mailTransport = $serviceLocator->get(
            'SlmMail\Mail\Transport\MandrillTransport'
        );

        $notifier = new EmailNotifier(
            $emailConfig, $mailTransport
        );

        return $notifier;
    }
} 
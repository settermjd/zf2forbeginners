<?php
namespace BabyMonitor;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use BabyMonitor\Notify\Feed\EmailNotifier;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'BabyMonitor\Tables\FeedTable' => 'BabyMonitor\Tables\Factories\FeedTableFactory',
                'BabyMonitor\Tables\FeedTableGateway' => 'BabyMonitor\Tables\Factories\FeedTablegatewayFactory',
                'BabyMonitor\Notify\Feed\EmailNotifier' => 'BabyMonitor\Notify\Feed\Factory\EmailNotifierFactory',
                'BabyMonitor\Cache\Application' => 'BabyMonitor\Cache\CacheFactory',
            )
        );
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                array(
                    __DIR__ . '/autoload_classmap.php'
                )
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $serviceManager = $e->getApplication()->getServiceManager();
        $sem = $eventManager->getSharedManager();

        $sem->attach('BabyMonitor\Controller\FeedsController', 'Feed.Create',
            function($e) use($serviceManager) {
                $notifier = $serviceManager->get('BabyMonitor\Notify\Feed\EmailNotifier');
                $notifier->notify(
                    $e->getParams()['feedData'], EmailNotifier::NOTIFY_CREATE
                );
            }
        );
    }
}

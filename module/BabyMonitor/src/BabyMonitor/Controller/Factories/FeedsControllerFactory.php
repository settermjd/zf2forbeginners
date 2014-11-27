<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This class allows the FeedsController to be instantiated as a factory by the ServiceManager
 *
 * @category   BabyMonitor
 * @package    Controller\Factories
 * @author     Matthew Setter <matthew@maltblue.com>
 * @copyright  2014 Matthew Setter <matthew@maltblue.com>
 * @since      File available since Release/Tag: 1.0
 */
namespace BabyMonitor\Controller\Factories;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    BabyMonitor\Controller\FeedsController;

class FeedsControllerFactory implements FactoryInterface
{
    protected $cache = null;
    protected $feedTable = null;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm = $serviceLocator->getServiceLocator();

        if ($sm->has('BabyMonitor\Tables\FeedTable')) {
            $this->feedTable = $sm->get('BabyMonitor\Tables\FeedTable');
        }

        if ($sm->has('BabyMonitor\Cache\Application')) {
            $this->cache = $sm->get('BabyMonitor\Cache\Application');
        }
        
        $config = $sm->get('Config');
        $appConfig = (array_key_exists('app', $config)) ? $config['app'] : null;
        
        $controller = new FeedsController($this->feedTable, $appConfig, $this->cache);

        return $controller;
    }
}